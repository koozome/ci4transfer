<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to(site_url('mypage'));
        }
        return redirect()->to(site_url('login'));
    }
}
