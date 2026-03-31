<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FileModel;

class Mypage extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (! auth()->loggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $fileModel = model(FileModel::class);
        $files     = $fileModel->where('user_id', auth()->id())
                               ->orderBy('created_at', 'DESC')
                               ->findAll();

        return $this->render('mypage', [
            'pageTitle' => 'マイページ',
            'files'     => $files,
        ]);
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if (! auth()->loggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $fileModel = model(FileModel::class);
        $file      = $fileModel->where('id', $id)->where('user_id', auth()->id())->first();

        if ($file === null) {
            return redirect()->to(site_url('mypage'))->with('errors', ['ファイルが見つかりません']);
        }

        $path = WRITEPATH . 'uploads/' . $file['stored_name'];
        if (is_file($path)) {
            unlink($path);
        }

        $fileModel->delete($id);

        return redirect()->to(site_url('mypage'))->with('message', 'ファイルを削除しました');
    }
}
