<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="page-header mb-3">
  <div class="row align-items-center">
    <div class="col">
      <h2 class="page-title"><?= esc($pageTitle) ?> - <?= esc($user->email) ?></h2>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?= site_url('admin/users/password/' . $user->id) ?>">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label class="form-label">新しいパスワード <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>">
        <?php if (isset($validation) && $validation->hasError('password')): ?>
          <div class="invalid-feedback"><?= esc($validation->getError('password')) ?></div>
        <?php endif ?>
      </div>

      <div class="mb-3">
        <label class="form-label">パスワード確認 <span class="text-danger">*</span></label>
        <input type="password" name="password_confirm" class="form-control <?= isset($validation) && $validation->hasError('password_confirm') ? 'is-invalid' : '' ?>">
        <?php if (isset($validation) && $validation->hasError('password_confirm')): ?>
          <div class="invalid-feedback"><?= esc($validation->getError('password_confirm')) ?></div>
        <?php endif ?>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">変更</button>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">キャンセル</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
