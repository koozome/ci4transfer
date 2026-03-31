<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\FileModel;

class Dashboard extends AdminController
{
    public function index(): string
    {
        $fileModel = model(FileModel::class);

        $data = [
            'pageTitle'    => 'ダッシュボード',
            'totalFiles'   => $fileModel->countAll(),
            'expiredFiles' => $fileModel->where('expires_at <', date('Y-m-d H:i:s'))->countAllResults(),
            'recentFiles'  => $fileModel->orderBy('created_at', 'DESC')->limit(10)->findAll(),
        ];

        return $this->render('admin/dashboard', $data);
    }
}
