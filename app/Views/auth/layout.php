<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= esc($title ?? 'ログイン') ?> — <?= esc(model(\App\Models\SettingModel::class)->getValue('site_name', 'ci4transfer')) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body class="bg-light">

<main role="main" class="container">
  <div class="d-flex justify-content-center p-5">
    <div class="card col-12 col-md-5 shadow-sm">
      <div class="card-body">
        <h4 class="text-center mb-4">
          <a href="<?= site_url('/') ?>" class="text-decoration-none text-dark">
            <?= esc(model(\App\Models\SettingModel::class)->getValue('site_name', 'ci4transfer')) ?>
          </a>
        </h4>
        <?php $desc = model(\App\Models\SettingModel::class)->getValue('site_description', ''); if ($desc !== ''): ?>
          <p class="text-center text-muted small mb-3"><?= esc($desc) ?></p>
        <?php endif ?>

        <?= $this->renderSection('content') ?>
      </div>
    </div>
  </div>
</main>

</body>
</html>
