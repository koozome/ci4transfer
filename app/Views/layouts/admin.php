<!DOCTYPE html>
<?php $theme = $siteSettings['admin_theme'] ?? 'light' ?>
<html lang="ja"<?= $theme !== 'auto' ? ' data-bs-theme="' . esc($theme) . '"' : '' ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? '管理画面') ?> - <?= esc($siteSettings['site_name'] ?? 'ci4transfer') ?></title>
  <?php if ($theme === 'auto'): ?>
  <script>document.documentElement.setAttribute('data-bs-theme', matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')</script>
  <?php endif ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css">
  <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
</head>
<body class="antialiased">
<div class="wrapper">

  <?= view('admin/_partials/sidebar') ?>

  <div class="page-wrapper">

    <?= view('admin/_partials/header') ?>

    <div class="page-body">
      <div class="container-fluid">

        <?php if (session()->getFlashdata('message')): ?>
          <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <div><?= esc(session()->getFlashdata('message')) ?></div>
            <a class="btn-close" data-bs-dismiss="alert"></a>
          </div>
        <?php endif ?>

        <?php if (session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
              <div><?= esc($e) ?></div>
            <?php endforeach ?>
            <a class="btn-close" data-bs-dismiss="alert"></a>
          </div>
        <?php endif ?>

        <?= $this->renderSection('content') ?>

      </div>
    </div>

    <footer class="footer footer-transparent d-print-none">
      <div class="container-fluid">
        <div class="row text-center align-items-center">
          <div class="col-12 col-lg-auto">
            <p class="text-secondary mb-0"><?= esc($siteSettings['copyright'] ?? '') ?></p>
          </div>
        </div>
      </div>
    </footer>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>
<script src="<?= base_url('js/admin.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
