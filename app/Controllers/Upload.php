<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FileModel;

class Upload extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (! auth()->loggedIn()) {
            return redirect()->to(site_url('login'));
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->store();
        }

        return $this->render('upload', [
            'pageTitle' => 'ファイルアップロード',
        ]);
    }

    private function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->request->getFile('file');

        if ($file === null || ! $file->isValid()) {
            return redirect()->back()->with('errors', ['ファイルが選択されていません']);
        }

        if ($file->hasMoved()) {
            return redirect()->back()->with('errors', ['アップロードに失敗しました']);
        }

        $expiresDays = (int) $this->request->getPost('expires_days');
        if (! in_array($expiresDays, [1, 3, 7, 30], true)) {
            $expiresDays = 7;
        }

        $ext        = $file->getClientExtension();
        $storedName = bin2hex(random_bytes(16)) . ($ext !== '' ? '.' . $ext : '');
        $token      = bin2hex(random_bytes(32));
        $password   = $this->request->getPost('password');

        $uploadPath = WRITEPATH . 'uploads/';
        $file->move($uploadPath, $storedName);

        $fileModel = model(FileModel::class);
        $fileModel->insert([
            'user_id'       => auth()->id(),
            'original_name' => $file->getClientName(),
            'stored_name'   => $storedName,
            'file_size'     => $file->getSize(),
            'token'         => $token,
            'password'      => $password !== '' && $password !== null ? password_hash($password, PASSWORD_DEFAULT) : null,
            'expires_at'    => date('Y-m-d H:i:s', strtotime("+{$expiresDays} days")),
            'download_count' => 0,
        ]);

        $downloadUrl = site_url('download/' . $token);

        return redirect()->to(site_url('mypage'))->with('message', "アップロード完了。ダウンロードURL: {$downloadUrl}");
    }
}
