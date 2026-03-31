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
    <form method="post" action="<?= site_url('admin/settings') ?>">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label class="form-label">サイト名 <span class="text-danger">*</span></label>
        <input type="text" name="site_name" class="form-control <?= isset($validation) && $validation->hasError('site_name') ? 'is-invalid' : '' ?>" value="<?= esc(old('site_name', $siteSettings['site_name'] ?? '')) ?>">
        <?php if (isset($validation) && $validation->hasError('site_name')): ?>
          <div class="invalid-feedback"><?= esc($validation->getError('site_name')) ?></div>
        <?php endif ?>
      </div>

      <div class="mb-3">
        <label class="form-label">コピーライト</label>
        <input type="text" name="copyright" class="form-control <?= isset($validation) && $validation->hasError('copyright') ? 'is-invalid' : '' ?>" value="<?= esc(old('copyright', $siteSettings['copyright'] ?? '')) ?>">
        <?php if (isset($validation) && $validation->hasError('copyright')): ?>
          <div class="invalid-feedback"><?= esc($validation->getError('copyright')) ?></div>
        <?php endif ?>
      </div>

      <div class="mb-3">
        <label class="form-label">管理画面テーマ</label>
        <?php $currentAdminTheme = old('admin_theme', $siteSettings['admin_theme'] ?? 'light') ?>
        <select name="admin_theme" class="form-select" style="max-width:240px">
          <option value="auto"  <?= $currentAdminTheme === 'auto'  ? 'selected' : '' ?>>Auto（OS設定に追従）</option>
          <option value="light" <?= $currentAdminTheme === 'light' ? 'selected' : '' ?>>Light</option>
          <option value="dark"  <?= $currentAdminTheme === 'dark'  ? 'selected' : '' ?>>Dark</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">公開面テーマ</label>
        <?php
          $currentPublicTheme = old('public_theme', $siteSettings['public_theme'] ?? 'light');
          $publicThemeOptions = [
            'auto'          => 'Auto（OS設定に追従）',
            'light'         => 'Light',
            'dark'          => 'Dark',
            'github'        => 'GitHub',
            'academic'      => 'Academic（セリフ体）',
            'onigiri'       => 'Onigiri（日本語向け）',
            'solarized'     => 'Solarized',
            'vue'           => 'Vue.js Docs',
            'monospace'     => 'Monospace',
            'night'         => 'Night（ダーク固定）',
            'monospace-dark'=> 'Monospace Dark（ダーク固定）',
          ];
        ?>
        <select name="public_theme" class="form-select" style="max-width:320px">
          <?php foreach ($publicThemeOptions as $val => $label): ?>
          <option value="<?= $val ?>"<?= $currentPublicTheme === $val ? ' selected' : '' ?>><?= $label ?></option>
          <?php endforeach ?>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">保存</button>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
