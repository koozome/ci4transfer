<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class Users extends AdminController
{
    private UserModel $userModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->requirePermission('users.manage');
        $this->userModel = model(UserModel::class);
    }

    public function index(): string
    {
        $users = $this->userModel->findAll();

        return $this->render('admin/users/index', [
            'pageTitle' => 'ユーザー管理',
            'users'     => $users,
        ]);
    }

    public function add(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email'            => 'required|valid_email|is_unique[auth_identities.secret]',
                'password'         => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
                'group'            => 'required|in_list[administrator,user]',
            ];

            if (! $this->validate($rules)) {
                return $this->render('admin/users/add', [
                    'pageTitle'  => 'ユーザー追加',
                    'validation' => $this->validator,
                ]);
            }

            $user = new User([
                'email'    => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
            ]);
            $this->userModel->save($user);
            $user = $this->userModel->findById($this->userModel->getInsertID());
            $user->syncGroups($this->request->getPost('group'));

            return redirect()->to(site_url('admin/users'))->with('message', 'ユーザーを追加しました');
        }

        return $this->render('admin/users/add', [
            'pageTitle' => 'ユーザー追加',
        ]);
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $user = $this->userModel->findById($id);
        if ($user === null) {
            return redirect()->to(site_url('admin/users'))->with('errors', ['ユーザーが見つかりません']);
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => "required|valid_email|is_unique[auth_identities.secret,id,{$id}]",
                'group' => 'required|in_list[administrator,user]',
            ];

            if (! $this->validate($rules)) {
                return $this->render('admin/users/edit', [
                    'pageTitle'  => 'ユーザー編集',
                    'user'       => $user,
                    'validation' => $this->validator,
                ]);
            }

            $user->email = $this->request->getPost('email');
            $this->userModel->save($user);
            $user->syncGroups($this->request->getPost('group'));

            return redirect()->to(site_url('admin/users'))->with('message', 'ユーザーを更新しました');
        }

        return $this->render('admin/users/edit', [
            'pageTitle' => 'ユーザー編集',
            'user'      => $user,
        ]);
    }

    public function password(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $user = $this->userModel->findById($id);
        if ($user === null) {
            return redirect()->to(site_url('admin/users'))->with('errors', ['ユーザーが見つかりません']);
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'password'         => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
            ];

            if (! $this->validate($rules)) {
                return $this->render('admin/users/password', [
                    'pageTitle'  => 'パスワード変更',
                    'user'       => $user,
                    'validation' => $this->validator,
                ]);
            }

            $user->password = $this->request->getPost('password');
            $this->userModel->save($user);

            return redirect()->to(site_url('admin/users'))->with('message', 'パスワードを変更しました');
        }

        return $this->render('admin/users/password', [
            'pageTitle' => 'パスワード変更',
            'user'      => $user,
        ]);
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($id === auth()->id()) {
            return redirect()->to(site_url('admin/users'))->with('errors', ['自分自身は削除できません']);
        }

        $user = $this->userModel->findById($id);
        if ($user === null) {
            return redirect()->to(site_url('admin/users'))->with('errors', ['ユーザーが見つかりません']);
        }

        $this->userModel->delete($id, true);

        return redirect()->to(site_url('admin/users'))->with('message', 'ユーザーを削除しました');
    }
}
