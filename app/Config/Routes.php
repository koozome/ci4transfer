<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Shield 認証ルート
service('auth')->routes($routes);

// ファイル操作（要ログイン）
$routes->group('', ['filter' => 'session'], static function (RouteCollection $routes): void {
    $routes->match(['GET', 'POST'], 'upload', 'Upload::index');
    $routes->get('mypage', 'Mypage::index');
    $routes->post('files/delete/(:num)', 'Mypage::delete/$1');
});

// ダウンロード（公開）
$routes->get('download/(:segment)', 'Download::index/$1');
$routes->post('download/(:segment)', 'Download::verify/$1');

// 管理ルート（要 admin.access 権限）
$routes->group('admin', ['filter' => 'session'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin\Dashboard::index');

    // ファイル管理
    $routes->get('files', 'Admin\Files::index');
    $routes->post('files/delete/(:num)', 'Admin\Files::delete/$1');

    // ユーザー管理
    $routes->get('users', 'Admin\Users::index');
    $routes->match(['GET', 'POST'], 'users/add', 'Admin\Users::add');
    $routes->match(['GET', 'POST'], 'users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->match(['GET', 'POST'], 'users/password/(:num)', 'Admin\Users::password/$1');
    $routes->post('users/delete/(:num)', 'Admin\Users::delete/$1');

    // サイト設定
    $routes->match(['GET', 'POST'], 'settings', 'Admin\Settings::index');
});
