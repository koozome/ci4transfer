<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /** @var array<string, array<string, string>> */
    public array $groups = [
        'administrator' => [
            'title'       => '管理者',
            'description' => '全権限。ユーザー管理、サイト設定、全ファイル管理',
        ],
        'user' => [
            'title'       => 'ユーザー',
            'description' => 'ファイルのアップロード・自分のファイル管理',
        ],
    ];

    /** @var array<string, string> */
    public array $permissions = [
        'admin.access'  => '管理画面アクセス',
        'admin.settings' => 'サイト設定',
        'users.manage'  => 'ユーザー管理',
        'files.manage'  => '全ファイル管理',
    ];

    /** @var array<string, list<string>> */
    public array $matrix = [
        'administrator' => [
            'admin.*',
            'users.*',
            'files.*',
        ],
        'user' => [],
    ];
}
