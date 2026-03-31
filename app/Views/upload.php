<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?= esc($pageTitle) ?></h3>
      </div>
      <div class="card-body">
        <form method="post" action="<?= site_url('upload') ?>" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="form-label">ファイル <span class="text-danger">*</span></label>
            <input type="file" name="file" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">有効期限</label>
            <select name="expires_days" class="form-select">
              <option value="1">1日</option>
              <option value="3">3日</option>
              <option value="7" selected>7日</option>
              <option value="30">30日</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">パスワード（任意）</label>
            <input type="password" name="password" class="form-control" placeholder="設定しない場合は空欄">
          </div>

          <button type="submit" class="btn btn-primary w-100">アップロード</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
