<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table         = 'files';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $allowedFields = [
        'user_id',
        'original_name',
        'stored_name',
        'file_size',
        'token',
        'password',
        'expires_at',
        'download_count',
    ];

    public function findByToken(string $token): ?array
    {
        return $this->where('token', $token)->first();
    }

    public function incrementDownload(int $id): void
    {
        $this->set('download_count', 'download_count + 1', false)->update($id);
    }

    public function getExpired(): array
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->findAll();
    }
}
