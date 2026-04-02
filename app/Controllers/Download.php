<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FileModel;
use CodeIgniter\HTTP\ResponseInterface;

class Download extends BaseController
{
    public function index(string $token): string
    {
        $file = $this->findValid($token);

        if (isset($file['error'])) {
            return $this->render('download', ['pageTitle' => $file['error'], 'error' => $file['error']]);
        }

        $autoDownload = $file['password'] === null || session()->get('dl_auth_' . $token) === true;

        return $this->render('download', [
            'pageTitle'      => 'ダウンロード',
            'file'           => $file,
            'requirePassword' => ! $autoDownload,
            'autoDownload'   => $autoDownload,
        ]);
    }

    public function verify(string $token): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->findValid($token);

        if (isset($file['error'])) {
            return redirect()->to(site_url('download/' . $token));
        }

        if (! password_verify((string) $this->request->getPost('password'), $file['password'])) {
            return $this->render('download', [
                'pageTitle'      => 'ダウンロード',
                'file'           => $file,
                'requirePassword' => true,
                'passwordError'  => 'パスワードが正しくありません',
            ]);
        }

        // パスワード認証済みをセッションに記録して情報ページへ（完了画面を表示するため）
        session()->set('dl_auth_' . $token, true);
        return redirect()->to(site_url('download/' . $token));
    }

    public function stream(string $token): string|ResponseInterface
    {
        $file = $this->findValid($token);

        if (isset($file['error'])) {
            return $this->render('download', ['pageTitle' => $file['error'], 'error' => $file['error']]);
        }

        // パスワード付きファイルはセッション認証を確認
        if ($file['password'] !== null) {
            if (! session()->get('dl_auth_' . $token)) {
                return redirect()->to(site_url('download/' . $token));
            }
            session()->remove('dl_auth_' . $token);
        }

        $path = WRITEPATH . 'uploads/' . $file['stored_name'];

        if (! is_file($path)) {
            return $this->render('download', ['pageTitle' => 'エラー', 'error' => 'ファイルが見つかりません']);
        }

        model(FileModel::class)->incrementDownload((int) $file['id']);

        return $this->response
            ->download($path, null)
            ->setFileName($file['original_name']);
    }

    private function findValid(string $token): array
    {
        $file = model(FileModel::class)->findByToken($token);

        if ($file === null) {
            return ['error' => 'このリンクは無効です'];
        }

        if ($file['expires_at'] !== null && strtotime($file['expires_at']) < time()) {
            return ['error' => 'このファイルの有効期限が切れています'];
        }

        return $file;
    }
}
