<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FileModel;
use CodeIgniter\HTTP\ResponseInterface;

class Download extends BaseController
{
    public function index(string $token): string|ResponseInterface
    {
        $fileModel = model(FileModel::class);
        $file      = $fileModel->findByToken($token);

        if ($file === null) {
            return $this->render('download', [
                'pageTitle' => 'ファイルが見つかりません',
                'error'     => 'このリンクは無効です',
            ]);
        }

        if ($file['expires_at'] !== null && strtotime($file['expires_at']) < time()) {
            return $this->render('download', [
                'pageTitle' => '有効期限切れ',
                'error'     => 'このファイルの有効期限が切れています',
            ]);
        }

        // パスワードなし → 直接ストリーム
        if ($file['password'] === null) {
            return $this->streamFile($file);
        }

        // パスワードあり → フォーム表示
        return $this->render('download', [
            'pageTitle'      => 'ダウンロード',
            'file'           => $file,
            'requirePassword' => true,
        ]);
    }

    public function verify(string $token): string|ResponseInterface
    {
        $fileModel = model(FileModel::class);
        $file      = $fileModel->findByToken($token);

        if ($file === null || ($file['expires_at'] !== null && strtotime($file['expires_at']) < time())) {
            return redirect()->to(site_url('download/' . $token));
        }

        $input = $this->request->getPost('password');

        if (! password_verify((string) $input, $file['password'])) {
            return $this->render('download', [
                'pageTitle'       => 'ダウンロード',
                'file'            => $file,
                'requirePassword' => true,
                'passwordError'   => 'パスワードが正しくありません',
            ]);
        }

        return $this->streamFile($file);
    }

    private function streamFile(array $file): ResponseInterface
    {
        $path = WRITEPATH . 'uploads/' . $file['stored_name'];

        if (! is_file($path)) {
            return $this->render('download', [
                'pageTitle' => 'エラー',
                'error'     => 'ファイルが見つかりません',
            ]);
        }

        model(FileModel::class)->incrementDownload((int) $file['id']);

        return $this->response
            ->download($path, null)
            ->setFileName($file['original_name']);
    }
}
