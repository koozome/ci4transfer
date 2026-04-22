<?= $this->extend('auth/layout') ?>
<?= $this->section('content') ?>

<h5 class="card-title mb-4"><?= lang('Auth.useMagicLink') ?></h5>

<?php if (session('error')): ?>
  <div class="alert alert-danger"><?= esc(session('error')) ?></div>
<?php elseif (session('errors')): ?>
  <div class="alert alert-danger">
    <?php foreach ((array) session('errors') as $error): ?>
      <div><?= esc($error) ?></div>
    <?php endforeach ?>
  </div>
<?php endif ?>

<form action="<?= url_to('magic-link') ?>" method="post">
  <?= csrf_field() ?>

  <div class="form-floating mb-3">
    <input type="email" class="form-control" id="email" name="email"
           autocomplete="email" placeholder="<?= lang('Auth.email') ?>"
           value="<?= old('email', auth()->user()->email ?? null) ?>" required>
    <label for="email"><?= lang('Auth.email') ?></label>
  </div>

  <div class="d-grid col-12 col-md-8 mx-auto mt-3">
    <button type="submit" class="btn btn-primary"><?= lang('Auth.send') ?></button>
  </div>
</form>

<p class="text-center mt-3"><a href="<?= url_to('login') ?>"><?= lang('Auth.backToLogin') ?></a></p>

<?= $this->endSection() ?>
