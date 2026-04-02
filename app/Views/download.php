<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card">
      <div class="card-body text-center py-5">

        <?php if (isset($error)): ?>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-danger mb-3" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4"/><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.871l-8.106 -13.534a1.914 1.914 0 0 0 -3.274 0z"/><path d="M12 16h.01"/></svg>
          <p class="text-danger"><?= esc($error) ?></p>

        <?php elseif (isset($file)): ?>

          <!-- ファイル情報 -->
          <div id="state-info">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-3" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M9 9l1 0"/><path d="M9 13l6 0"/><path d="M9 17l6 0"/></svg>
            <h3 class="mb-1"><?= esc($file['original_name']) ?></h3>
            <p class="text-secondary mb-4">
              <?= esc(number_format($file['file_size'] / 1048576, 1)) ?> MB
              &nbsp;·&nbsp;
              期限: <?= esc($file['expires_at']) ?>
              &nbsp;·&nbsp;
              DL数: <?= esc($file['download_count']) ?>
            </p>

            <?php if (isset($requirePassword) && $requirePassword): ?>
              <?php if (isset($passwordError)): ?>
                <div class="alert alert-danger mb-3"><?= esc($passwordError) ?></div>
              <?php endif ?>
              <form method="post" action="<?= site_url('download/' . $file['token']) ?>" class="text-start">
                <?= csrf_field() ?>
                <div class="mb-3">
                  <label class="form-label">パスワード</label>
                  <input type="password" name="password" class="form-control" autofocus required>
                </div>
                <button type="submit" class="btn btn-primary w-100">ダウンロード</button>
              </form>
            <?php else: ?>
              <button id="dl-btn" class="btn btn-primary btn-lg px-5" onclick="startDownload()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 11l5 5l5 -5"/><path d="M12 4l0 12"/></svg>
                ダウンロード
              </button>
            <?php endif ?>
            <?php if (! empty($autoDownload)): ?>
              <script>document.addEventListener('DOMContentLoaded', () => startDownload());</script>
            <?php endif ?>
          </div>

          <!-- 完了画面（JS で切替） -->
          <div id="state-done" style="display:none">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-success mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
            <h3 class="text-success mb-2">ダウンロード完了</h3>
            <p class="text-secondary"><?= esc($file['original_name']) ?></p>
          </div>

        <?php endif ?>

      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (isset($file) && (! isset($requirePassword) || ! $requirePassword)): ?>
<script>
function startDownload() {
  document.getElementById('state-info').style.display = 'none';
  document.getElementById('state-done').style.display = 'block';

  const iframe = document.createElement('iframe');
  iframe.style.display = 'none';
  iframe.src = '<?= site_url('download/' . $file['token'] . '/stream') ?>';
  document.body.appendChild(iframe);
}
</script>
<?php endif ?>
<?= $this->endSection() ?>
