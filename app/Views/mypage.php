<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0"><?= esc($pageTitle) ?></h2>
  <a href="<?= site_url('upload') ?>" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
    アップロード
  </a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-vcenter card-table">
      <thead>
        <tr>
          <th>ファイル名</th>
          <th>サイズ</th>
          <th>有効期限</th>
          <th>DL数</th>
          <th>ダウンロードURL</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($files)): ?>
          <tr><td colspan="6" class="text-center text-secondary py-4">アップロードされたファイルはありません</td></tr>
        <?php else: ?>
          <?php foreach ($files as $file): ?>
            <?php $expired = $file['expires_at'] !== null && strtotime($file['expires_at']) < time() ?>
            <tr class="<?= $expired ? 'text-secondary' : '' ?>">
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
              <td><?= esc($file['expires_at']) ?></td>
              <td><?= esc($file['download_count']) ?></td>
              <td>
                <?php if (! $expired): ?>
                  <?php $url = site_url('download/' . $file['token']) ?>
                  <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-sm" value="<?= esc($url) ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyUrl(this, '<?= esc($url) ?>')">コピー</button>
                  </div>
                <?php endif ?>
              </td>
              <td>
                <form method="post" action="<?= site_url('files/delete/' . $file['id']) ?>" onsubmit="return confirm('削除しますか？')">
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

<?= $this->section('scripts') ?>
<script>
function copyUrl(btn, url) {
  navigator.clipboard.writeText(url).then(() => {
    const orig = btn.textContent;
    btn.textContent = '✓';
    btn.classList.add('btn-success');
    btn.classList.remove('btn-outline-secondary');
    setTimeout(() => {
      btn.textContent = orig;
      btn.classList.remove('btn-success');
      btn.classList.add('btn-outline-secondary');
    }, 1500);
  });
}
</script>
<?= $this->endSection() ?>
