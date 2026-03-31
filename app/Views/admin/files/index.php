<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="page-header mb-3">
  <div class="row align-items-center">
    <div class="col">
      <h2 class="page-title"><?= esc($pageTitle) ?></h2>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-vcenter card-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>ファイル名</th>
          <th>サイズ</th>
          <th>ユーザーID</th>
          <th>有効期限</th>
          <th>DL数</th>
          <th>アップロード日時</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($files)): ?>
          <tr><td colspan="8" class="text-center text-secondary py-4">データなし</td></tr>
        <?php else: ?>
          <?php foreach ($files as $file): ?>
            <?php $expired = $file['expires_at'] !== null && strtotime($file['expires_at']) < time() ?>
            <tr>
              <td class="text-secondary"><?= esc($file['id']) ?></td>
              <td>
                <?= esc($file['original_name']) ?>
                <?php if ($file['password'] !== null): ?>
                  <span class="badge bg-yellow-lt ms-1">PW</span>
                <?php endif ?>
                <?php if ($expired): ?>
                  <span class="badge bg-red-lt ms-1">期限切れ</span>
                <?php endif ?>
              </td>
              <td><?= esc(number_format($file['file_size'] / 1048576, 1)) ?> MB</td>
              <td class="text-secondary"><?= esc($file['user_id'] ?? '-') ?></td>
              <td><?= esc($file['expires_at']) ?></td>
              <td><?= esc($file['download_count']) ?></td>
              <td><?= esc($file['created_at']) ?></td>
              <td>
                <form method="post" action="<?= site_url('admin/files/delete/' . $file['id']) ?>" onsubmit="return confirm('削除しますか？')">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                </form>
              </td>
            </tr>
          <?php endforeach ?>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
