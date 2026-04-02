<!DOCTYPE html>
<?php
  $theme  = $siteSettings['public_theme'] ?? 'light';
  $themes = [
    'auto'          => ['dark' => false, 'fixed' => false, 'css' => null],
    'light'         => ['dark' => false, 'fixed' => false, 'css' => null],
    'dark'          => ['dark' => false, 'fixed' => true,  'css' => null],
    'github'        => ['dark' => false, 'fixed' => false, 'css' => 'github'],
    'academic'      => ['dark' => false, 'fixed' => false, 'css' => 'academic'],
    'onigiri'       => ['dark' => true,  'fixed' => false, 'css' => 'onigiri'],
    'solarized'     => ['dark' => true,  'fixed' => false, 'css' => 'solarized'],
    'vue'           => ['dark' => true,  'fixed' => false, 'css' => 'vue'],
    'monospace'     => ['dark' => true,  'fixed' => false, 'css' => 'monospace'],
    'night'         => ['dark' => false, 'fixed' => true,  'css' => 'night'],
    'monospace-dark'=> ['dark' => false, 'fixed' => true,  'css' => 'monospace-dark'],
  ];
  $t = $themes[$theme] ?? $themes['light'];
  $lightMedia = '(prefers-color-scheme: light), (prefers-color-scheme: no-preference)';
  $darkMedia  = '(prefers-color-scheme: dark)';
  // Tablerのdata-bs-theme: fixed=trueはdark、autoはJS、それ以外はlight
  $bsTheme = $t['fixed'] ? 'dark' : ($theme === 'auto' ? null : 'light');
?>
<html lang="ja"<?= $bsTheme !== null ? ' data-bs-theme="' . $bsTheme . '"' : '' ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? '') ?><?= isset($pageTitle) ? ' - ' : '' ?><?= esc($siteSettings['site_name'] ?? 'ci4transfer') ?></title>
  <?php if ($theme === 'auto'): ?>
  <script>document.documentElement.setAttribute('data-bs-theme', matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')</script>
  <?php endif ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css">
  <?php if ($t['css'] !== null): ?>
    <?php if ($t['fixed']): ?>
      <link rel="stylesheet" href="<?= base_url('css/themes/' . $t['css'] . '.css') ?>">
    <?php else: ?>
      <link rel="stylesheet" href="<?= base_url('css/themes/' . $t['css'] . '.css') ?>" media="<?= $lightMedia ?>">
      <?php if ($t['dark']): ?>
      <link rel="stylesheet" href="<?= base_url('css/themes/' . $t['css'] . '-dark.css') ?>" media="<?= $darkMedia ?>">
      <?php endif ?>
    <?php endif ?>
  <?php endif ?>
</head>
<body class="antialiased d-flex flex-column min-vh-100">

<header class="navbar navbar-expand-md navbar-light bg-white border-bottom">
  <div class="container">
    <a href="<?= site_url('/') ?>" class="navbar-brand fw-bold"><?= esc($siteSettings['site_name'] ?? 'ci4transfer') ?></a>
    <div class="ms-auto d-flex gap-2">
      <?php if (auth()->loggedIn()): ?>
        <?php if (auth()->user()->can('admin.access')): ?>
          <a href="<?= site_url('admin') ?>" class="btn btn-outline-secondary btn-sm" title="管理画面">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon m-0" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/></svg>
          </a>
        <?php endif ?>
        <a href="<?= site_url('mypage') ?>" class="btn btn-outline-secondary btn-sm">マイページ</a>
        <a href="<?= site_url('upload') ?>" class="btn btn-primary btn-sm">アップロード</a>
        <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger btn-sm">ログアウト</a>
      <?php else: ?>
        <a href="<?= site_url('login') ?>" class="btn btn-outline-secondary btn-sm">ログイン</a>
      <?php endif ?>
    </div>
  </div>
</header>

<main class="flex-grow-1 py-4">
  <div class="container">
    <?php if (session()->getFlashdata('message')): ?>
      <div class="alert alert-success alert-dismissible mb-3">
        <div><?= esc(session()->getFlashdata('message')) ?></div>
        <a class="btn-close" data-bs-dismiss="alert"></a>
      </div>
    <?php endif ?>
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger alert-dismissible mb-3">
        <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
          <div><?= esc($e) ?></div>
        <?php endforeach ?>
        <a class="btn-close" data-bs-dismiss="alert"></a>
      </div>
    <?php endif ?>
    <?= $this->renderSection('content') ?>
  </div>
</main>

<footer class="border-top py-3 text-center text-secondary small">
  <?= esc($siteSettings['copyright'] ?? '') ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
