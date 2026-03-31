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
  <div class="card-body">
    <form method="post" action="<?= site_url('admin/users/edit/' . $user->id) ?>">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label class="form-label">メールアドレス <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" value="<?= esc(old('email', $user->email)) ?>">
        <?php if (isset($validation) && $validation->hasError('email')): ?>
          <div class="invalid-feedback"><?= esc($validation->getError('email')) ?></div>
        <?php endif ?>
      </div>

      <div class="mb-3">
        <label class="form-label">グループ <span class="text-danger">*</span></label>
        <?php $currentGroup = $user->getGroups()[0] ?? 'user' ?>
        <select name="group" class="form-select <?= isset($validation) && $validation->hasError('group') ? 'is-invalid' : '' ?>">
          <option value="user" <?= old('group', $currentGroup) === 'user' ? 'selected' : '' ?>>user</option>
          <option value="administrator" <?= old('group', $currentGroup) === 'administrator' ? 'selected' : '' ?>>administrator</option>
        </select>
        <?php if (isset($validation) && $validation->hasError('group')): ?>
          <div class="invalid-feedback"><?= esc($validation->getError('group')) ?></div>
        <?php endif ?>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">更新</button>
        <a href="<?= site_url('admin/users/password/' . $user->id) ?>" class="btn btn-outline-secondary">パスワード変更</a>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">キャンセル</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
