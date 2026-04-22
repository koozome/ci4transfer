<?= $this->extend('auth/layout') ?>
<?= $this->section('content') ?>

<h5 class="card-title mb-4"><?= lang('Auth.useMagicLink') ?></h5>

<p><strong><?= lang('Auth.checkYourEmail') ?></strong></p>
<p><?= lang('Auth.magicLinkDetails', [setting('Auth.magicLinkLifetime') / 60]) ?></p>

<p class="text-center mt-3"><a href="<?= url_to('login') ?>"><?= lang('Auth.backToLogin') ?></a></p>

<?= $this->endSection() ?>
