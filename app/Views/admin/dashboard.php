<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="page-header mb-3">
  <div class="row align-items-center">
    <div class="col">
      <h2 class="page-title"><?= esc($pageTitle) ?></h2>
    </div>
  </div>
</div>

<div class="row row-cards">
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">総ファイル数</div>
        <div class="h1"><?= esc($totalFiles) ?></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">期限切れ</div>
        <div class="h1 text-danger"><?= esc($expiredFiles) ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header">
    <h3 class="card-title">最近のアップロード</h3>
  </div>
  <div class="table-responsive">
    <table class="table table-vcenter card-table">
      <thead>
        <tr>
          <th>ファイル名</th>
          <th>サイズ</th>
          <th>有効期限</th>
          <th>DL数</th>
          <th>日時</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentFiles)): ?>
          <tr><td colspan="5" class="text-center text-secondary">データなし</td></tr>
        <?php else: ?>
          <?php foreach ($recentFiles as $file): ?>
            <tr>
              <td><?= esc($file['original_name']) ?></td>
              <td><?= esc(number_format($file['file_size'] / 1048576, 1)) ?> MB</td>
              <td><?= esc($file['expires_at']) ?></td>
              <td><?= esc($file['download_count']) ?></td>
              <td><?= esc($file['created_at']) ?></td>
            </tr>
          <?php endforeach ?>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
