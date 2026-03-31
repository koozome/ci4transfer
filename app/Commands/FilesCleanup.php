<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\FileModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FilesCleanup extends BaseCommand
{
    protected $group       = 'Files';
    protected $name        = 'files:cleanup';
    protected $description = '期限切れファイルのDBレコードと物理ファイルを削除する';

    public function run(array $params): void
    {
        $fileModel = model(FileModel::class);
        $expired   = $fileModel->getExpired();

        if (empty($expired)) {
            CLI::write('期限切れファイルはありません', 'yellow');
            return;
        }

        $deleted = 0;
        $missing = 0;

        foreach ($expired as $file) {
            $path = WRITEPATH . 'uploads/' . $file['stored_name'];

            if (is_file($path)) {
                unlink($path);
                $deleted++;
            } else {
                $missing++;
            }

            $fileModel->delete((int) $file['id']);

            CLI::write(sprintf(
                '削除: [%d] %s (%s)',
                $file['id'],
                $file['original_name'],
                $file['expires_at']
            ));
        }

        CLI::write(sprintf(
            '完了: %d件削除（物理ファイル未存在: %d件）',
            count($expired),
            $missing
        ), 'green');
    }
}
