<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\FileModel;

class Files extends AdminController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->requirePermission('files.manage');
    }

    public function index(): string
    {
        $fileModel = model(FileModel::class);

        $files = $fileModel->orderBy('created_at', 'DESC')->findAll();

        return $this->render('admin/files/index', [
            'pageTitle' => 'ファイル管理',
            'files'     => $files,
        ]);
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $fileModel = model(FileModel::class);
        $file      = $fileModel->find($id);

        if ($file === null) {
            return redirect()->to(site_url('admin/files'))->with('errors', ['ファイルが見つかりません']);
        }

        $path = WRITEPATH . 'uploads/' . $file['stored_name'];
        if (is_file($path)) {
            unlink($path);
        }

        $fileModel->delete($id);

        return redirect()->to(site_url('admin/files'))->with('message', 'ファイルを削除しました');
    }
}
