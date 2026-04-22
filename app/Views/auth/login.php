<?= $this->extend('auth/layout') ?>
<?= $this->section('content') ?>

<h5 class="card-title mb-4"><?= lang('Auth.login') ?></h5>

<?php if (session('error')): ?>
  <div class="alert alert-danger"><?= esc(session('error')) ?></div>
<?php elseif (session('errors')): ?>
  <div class="alert alert-danger">
    <?php foreach ((array) session('errors') as $error): ?>
      <div><?= esc($error) ?></div>
    <?php endforeach ?>
  </div>
<?php endif ?>

<?php if (session('message')): ?>
  <div class="alert alert-success"><?= esc(session('message')) ?></div>
<?php endif ?>

<form action="<?= url_to('login') ?>" method="post">
  <?= csrf_field() ?>

  <div class="form-floating mb-3">
    <input type="email" class="form-control" id="email" name="email"
           autocomplete="email" placeholder="you@example.com"
           value="<?= old('email') ?>" required>
    <label for="email"><?= lang('Auth.email') ?></label>
  </div>

  <div class="form-floating mb-3">
    <input type="password" class="form-control" id="password" name="password"
           autocomplete="current-password" placeholder="password" required>
    <label for="password"><?= lang('Auth.password') ?></label>
  </div>

  <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
    <div class="form-check mb-3">
      <label class="form-check-label">
        <input type="checkbox" name="remember" class="form-check-input"
               <?= old('remember') ? 'checked' : '' ?>>
        <?= lang('Auth.rememberMe') ?>
      </label>
    </div>
  <?php endif ?>

  <div class="d-grid col-12 col-md-8 mx-auto mt-3">
    <button type="submit" class="btn btn-primary"><?= lang('Auth.login') ?></button>
  </div>
</form>

<?php if (setting('Auth.allowMagicLinkLogins')): ?>
  <p class="text-center mt-3"><?= lang('Auth.forgotPassword') ?> <a href="<?= url_to('magic-link') ?>"><?= lang('Auth.useMagicLink') ?></a></p>
<?php endif ?>
<?php if (setting('Auth.allowRegistration')): ?>
  <p class="text-center"><?= lang('Auth.noAccount') ?> <a href="<?= url_to('register') ?>"><?= lang('Auth.register') ?></a></p>
<?php endif ?>

<?= $this->endSection() ?>
