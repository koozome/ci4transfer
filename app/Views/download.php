<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card">
      <div class="card-body text-center py-5">

        <?php if (isset($error)): ?>
          <div class="text-danger mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4"/><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.871l-8.106 -13.534a1.914 1.914 0 0 0 -3.274 0z"/><path d="M12 16h.01"/></svg>
            <p><?= esc($error) ?></p>
          </div>

        <?php elseif (isset($requirePassword) && $requirePassword): ?>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-3" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"/><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"/><path d="M8 11v-4a4 4 0 1 1 8 0v4"/></svg>
          <h3 class="mb-1"><?= esc($file['original_name']) ?></h3>
          <p class="text-secondary mb-4"><?= esc(number_format($file['file_size'] / 1048576, 1)) ?> MB &nbsp;·&nbsp; 期限: <?= esc($file['expires_at']) ?></p>

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

        <?php endif ?>

      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
