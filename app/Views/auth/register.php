<?= $this->extend('auth/layout') ?>
<?= $this->section('content') ?>

<h5 class="card-title mb-4"><?= lang('Auth.register') ?></h5>

<?php if (session('error')): ?>
  <div class="alert alert-danger"><?= esc(session('error')) ?></div>
<?php elseif (session('errors')): ?>
  <div class="alert alert-danger">
    <?php foreach ((array) session('errors') as $error): ?>
      <div><?= esc($error) ?></div>
    <?php endforeach ?>
  </div>
<?php endif ?>

<form action="<?= url_to('register') ?>" method="post">
  <?= csrf_field() ?>

  <div class="form-floating mb-3">
    <input type="email" class="form-control" id="email" name="email"
           inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>"
           value="<?= old('email') ?>" required>
    <label for="email"><?= lang('Auth.email') ?></label>
  </div>

  <div class="form-floating mb-3">
    <input type="text" class="form-control" id="username" name="username"
           inputmode="text" autocomplete="username" placeholder="<?= lang('Auth.username') ?>"
           value="<?= old('username') ?>" required>
    <label for="username"><?= lang('Auth.username') ?></label>
  </div>

  <div class="form-floating mb-3">
    <input type="password" class="form-control" id="password" name="password"
           autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" required>
    <label for="password"><?= lang('Auth.password') ?></label>
  </div>

  <div class="form-floating mb-3">
    <input type="password" class="form-control" id="password_confirm" name="password_confirm"
           autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" required>
    <label for="password_confirm"><?= lang('Auth.passwordConfirm') ?></label>
  </div>

  <div class="d-grid col-12 col-md-8 mx-auto mt-3">
    <button type="submit" class="btn btn-primary"><?= lang('Auth.register') ?></button>
  </div>
</form>

<p class="text-center mt-3"><?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a></p>

<?= $this->endSection() ?>
