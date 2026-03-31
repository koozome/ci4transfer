<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\SettingModel;

class Settings extends AdminController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->requirePermission('admin.settings');
    }

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $settingModel = model(SettingModel::class);

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'site_name'    => 'required|max_length[100]',
                'copyright'    => 'permit_empty|max_length[200]',
                'admin_theme'  => 'required|in_list[auto,light,dark]',
                'public_theme' => 'required|in_list[auto,light,dark,github,academic,onigiri,solarized,vue,monospace,night,monospace-dark]',
            ];

            if (! $this->validate($rules)) {
                return $this->render('admin/settings/index', [
                    'pageTitle'  => 'サイト設定',
                    'validation' => $this->validator,
                ]);
            }

            $settingModel->setValue('site_name',    $this->request->getPost('site_name'));
            $settingModel->setValue('copyright',    $this->request->getPost('copyright') ?? '');
            $settingModel->setValue('admin_theme',  $this->request->getPost('admin_theme'));
            $settingModel->setValue('public_theme', $this->request->getPost('public_theme'));

            return redirect()->to(site_url('admin/settings'))->with('message', '設定を保存しました');
        }

        return $this->render('admin/settings/index', [
            'pageTitle' => 'サイト設定',
        ]);
    }
}
