<?php
/**
 * ci4transfer Web Installer
 * アクセス後は必ず /public/install/ ディレクトリを削除すること
 */

session_start();

define('ROOT_PATH', dirname(__DIR__, 2) . '/');
define('ENV_PATH',  ROOT_PATH . '.env');

$step = $_GET['step'] ?? 'requirements';

// ----------------------------------------
// ヘルパー関数
// ----------------------------------------

function check_requirements(): array
{
    $errors = [];

    if (PHP_VERSION_ID < 80200) {
        $errors[] = 'PHP 8.2 以上が必要です（現在: ' . PHP_VERSION . '）';
    }

    foreach (['intl', 'mbstring', 'mysqli', 'pdo_mysql', 'zip'] as $ext) {
        if (! extension_loaded($ext)) {
            $errors[] = "PHP 拡張 {$ext} が必要です";
        }
    }

    $writableDirs = [
        ROOT_PATH . 'writable/cache',
        ROOT_PATH . 'writable/logs',
        ROOT_PATH . 'writable/session',
        ROOT_PATH . 'writable/uploads',
    ];
    foreach ($writableDirs as $dir) {
        if (! is_writable($dir)) {
            $errors[] = "書き込み権限が必要: {$dir}";
        }
    }

    if (file_exists(ENV_PATH) && ! is_writable(ENV_PATH)) {
        $errors[] = '.env ファイルの書き込み権限が必要です';
    }
    if (! file_exists(ENV_PATH) && ! is_writable(ROOT_PATH)) {
        $errors[] = 'プロジェクトルートへの書き込み権限が必要です';
    }

    return $errors;
}

function try_db(string $host, string $port, string $db, string $user, string $pass): ?string
{
    try {
        new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return null;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function write_env(array $db, string $baseURL, string $encKey): void
{
    $template = file_get_contents(ROOT_PATH . 'env');

    $replacements = [
        '# CI_ENVIRONMENT = production'       => 'CI_ENVIRONMENT = production',
        '# app.baseURL = \'\''                => "app.baseURL = '{$baseURL}'",
        '# app.encryptionKey = '              => "app.encryptionKey = hex2bin:{$encKey}",
        '# database.default.hostname = localhost' => "database.default.hostname = {$db['host']}",
        '# database.default.database = ci4'       => "database.default.database = {$db['name']}",
        '# database.default.username = root'      => "database.default.username = {$db['user']}",
        '# database.default.password = root'      => "database.default.password = {$db['pass']}",
        '# database.default.port = 3306'          => "database.default.port = {$db['port']}",
        '# database.default.DBDriver = MySQLi'    => 'database.default.DBDriver = MySQLi',
    ];

    $content = str_replace(array_keys($replacements), array_values($replacements), $template);
    file_put_contents(ENV_PATH, $content);
}

function run_migrations(): array
{
    $sparkPath = ROOT_PATH . 'spark';
    $output    = [];
    $code      = 0;

    exec('php ' . escapeshellarg($sparkPath) . ' migrate --all --no-interaction 2>&1', $output, $code);

    return ['output' => implode("\n", $output), 'success' => $code === 0];
}

function create_admin(string $email, string $password, array $db): ?string
{
    try {
        $pdo = new PDO(
            "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4",
            $db['user'], $db['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $now = date('Y-m-d H:i:s');

        // users テーブルに挿入
        $pdo->prepare('INSERT INTO users (active, created_at, updated_at) VALUES (1, ?, ?)')
            ->execute([$now, $now]);
        $userId = (int) $pdo->lastInsertId();

        // auth_identities にメール＋パスワードを登録
        $pdo->prepare(
            'INSERT INTO auth_identities (user_id, type, secret, secret2, created_at, updated_at)
             VALUES (?, \'email_password\', ?, ?, ?, ?)'
        )->execute([$userId, $email, password_hash($password, PASSWORD_DEFAULT), $now, $now]);

        // administrator グループに追加
        $pdo->prepare(
            'INSERT INTO auth_groups_users (user_id, `group`, created_at) VALUES (?, \'administrator\', ?)'
        )->execute([$userId, $now]);

        // transfer_settings のデフォルト値を更新（site_name/copyright）
        if (! empty($_SESSION['install']['site_name'])) {
            $pdo->prepare('UPDATE transfer_settings SET setting_value=?, updated_at=? WHERE setting_key=\'site_name\'')
                ->execute([$_SESSION['install']['site_name'], $now]);
        }
        if (! empty($_SESSION['install']['copyright'])) {
            $pdo->prepare('UPDATE transfer_settings SET setting_value=?, updated_at=? WHERE setting_key=\'copyright\'')
                ->execute([$_SESSION['install']['copyright'], $now]);
        }

        return null;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ----------------------------------------
// ステップ処理
// ----------------------------------------

$errors  = [];
$success = false;

if ($step === 'requirements') {
    $errors = check_requirements();
    $ok     = empty($errors);
}

if ($step === 'database' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = [
        'host' => trim($_POST['db_host'] ?? 'localhost'),
        'port' => trim($_POST['db_port'] ?? '3306'),
        'name' => trim($_POST['db_name'] ?? ''),
        'user' => trim($_POST['db_user'] ?? ''),
        'pass' => $_POST['db_pass'] ?? '',
    ];

    if (empty($db['name']) || empty($db['user'])) {
        $errors[] = 'データベース名とユーザー名は必須です';
    } else {
        $connErr = try_db($db['host'], $db['port'], $db['name'], $db['user'], $db['pass']);
        if ($connErr) {
            $errors[] = 'DB接続エラー: ' . $connErr;
        } else {
            $_SESSION['install']['db'] = $db;
            header('Location: ?step=site');
            exit;
        }
    }
}

if ($step === 'site' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName  = trim($_POST['site_name']  ?? '');
    $baseURL   = rtrim(trim($_POST['base_url'] ?? ''), '/') . '/';
    $copyright = trim($_POST['copyright']  ?? '');
    $adminEmail = trim($_POST['admin_email']    ?? '');
    $adminPass  = $_POST['admin_password']      ?? '';
    $adminPass2 = $_POST['admin_password2']     ?? '';

    if (empty($siteName))    $errors[] = 'サイト名は必須です';
    if (empty($baseURL))     $errors[] = 'ベースURLは必須です';
    if (empty($adminEmail))  $errors[] = '管理者メールアドレスは必須です';
    if (strlen($adminPass) < 8) $errors[] = 'パスワードは8文字以上';
    if ($adminPass !== $adminPass2) $errors[] = 'パスワードが一致しません';

    if (empty($errors)) {
        $_SESSION['install']['site_name']  = $siteName;
        $_SESSION['install']['base_url']   = $baseURL;
        $_SESSION['install']['copyright']  = $copyright;
        $_SESSION['install']['admin_email'] = $adminEmail;
        $_SESSION['install']['admin_pass']  = $adminPass;
        header('Location: ?step=install');
        exit;
    }
}

if ($step === 'install' && ! isset($_SESSION['install']['done'])) {
    $s = $_SESSION['install'] ?? [];

    if (empty($s['db']) || empty($s['admin_email'])) {
        header('Location: ?step=requirements');
        exit;
    }

    $encKey = bin2hex(random_bytes(32));
    write_env($s['db'], $s['base_url'], $encKey);

    $migrate = run_migrations();
    if (! $migrate['success']) {
        $errors[] = 'マイグレーション失敗:' . "\n" . $migrate['output'];
    } else {
        $adminErr = create_admin($s['admin_email'], $s['admin_pass'], $s['db']);
        if ($adminErr) {
            $errors[] = '管理者作成失敗: ' . $adminErr;
        } else {
            $_SESSION['install']['done'] = true;
        }
    }

    if (empty($errors)) {
        header('Location: ?step=complete');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ci4transfer インストーラー</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css">
</head>
<body class="antialiased bg-body-secondary d-flex flex-column min-vh-100">
<div class="container py-5" style="max-width:640px">

  <div class="text-center mb-4">
    <h1 class="h3">ci4transfer インストーラー</h1>
  </div>

  <!-- ステップナビ -->
  <div class="steps mb-4">
    <?php
      $stepList = ['requirements' => '動作確認', 'database' => 'データベース', 'site' => 'サイト設定', 'install' => 'インストール', 'complete' => '完了'];
      $reached  = false;
      foreach ($stepList as $k => $label):
        $active  = $k === $step;
        if ($active) $reached = true;
        $done    = ! $reached && ! $active;
    ?>
    <div class="step-item <?= $active ? 'active' : ($done ? 'step-item-done' : '') ?>">
      <div class="step-link">
        <span class="step-number"><?= $done ? '✓' : (array_search($k, array_keys($stepList)) + 1) ?></span>
        <span class="step-title"><?= $label ?></span>
      </div>
    </div>
    <?php endforeach ?>
  </div>

  <!-- エラー -->
  <?php if (! empty($errors)): ?>
  <div class="alert alert-danger mb-4">
    <?php foreach ($errors as $e): ?>
      <div><?= h($e) ?></div>
    <?php endforeach ?>
  </div>
  <?php endif ?>

  <div class="card">
    <div class="card-body">

    <!-- STEP 1: 動作確認 -->
    <?php if ($step === 'requirements'): ?>
      <h3 class="card-title">動作環境の確認</h3>
      <?php if (empty($errors)): ?>
        <div class="alert alert-success">すべての要件を満たしています</div>
        <a href="?step=database" class="btn btn-primary mt-2">次へ</a>
      <?php else: ?>
        <p class="text-secondary">以下の問題を解決してから再度確認してください。</p>
        <a href="?step=requirements" class="btn btn-outline-secondary mt-2">再確認</a>
      <?php endif ?>

    <!-- STEP 2: データベース -->
    <?php elseif ($step === 'database'): ?>
      <h3 class="card-title">データベース設定</h3>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">ホスト</label>
          <input type="text" name="db_host" class="form-control" value="<?= h($_POST['db_host'] ?? 'localhost') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">ポート</label>
          <input type="text" name="db_port" class="form-control" style="max-width:120px" value="<?= h($_POST['db_port'] ?? '3306') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">データベース名 <span class="text-danger">*</span></label>
          <input type="text" name="db_name" class="form-control" value="<?= h($_POST['db_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">ユーザー名 <span class="text-danger">*</span></label>
          <input type="text" name="db_user" class="form-control" value="<?= h($_POST['db_user'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">パスワード</label>
          <input type="password" name="db_pass" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">接続確認して次へ</button>
      </form>

    <!-- STEP 3: サイト設定 -->
    <?php elseif ($step === 'site'): ?>
      <h3 class="card-title">サイト設定 &amp; 管理者アカウント</h3>
      <form method="post">
        <h4 class="mt-3 mb-2 fs-5">サイト設定</h4>
        <div class="mb-3">
          <label class="form-label">サイト名 <span class="text-danger">*</span></label>
          <input type="text" name="site_name" class="form-control" value="<?= h($_POST['site_name'] ?? 'ci4transfer') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">ベースURL <span class="text-danger">*</span></label>
          <input type="url" name="base_url" class="form-control" value="<?= h($_POST['base_url'] ?? (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">コピーライト</label>
          <input type="text" name="copyright" class="form-control" value="<?= h($_POST['copyright'] ?? '© ' . date('Y') . ' ci4transfer') ?>">
        </div>
        <hr>
        <h4 class="mt-3 mb-2 fs-5">管理者アカウント</h4>
        <div class="mb-3">
          <label class="form-label">メールアドレス <span class="text-danger">*</span></label>
          <input type="email" name="admin_email" class="form-control" value="<?= h($_POST['admin_email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">パスワード（8文字以上） <span class="text-danger">*</span></label>
          <input type="password" name="admin_password" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">パスワード確認 <span class="text-danger">*</span></label>
          <input type="password" name="admin_password2" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">インストール開始</button>
      </form>

    <!-- STEP 4: インストール実行 -->
    <?php elseif ($step === 'install'): ?>
      <h3 class="card-title">インストール中...</h3>
      <?php if (empty($errors)): ?>
        <div class="d-flex align-items-center gap-2 text-secondary">
          <div class="spinner-border spinner-border-sm"></div>
          処理中です。しばらくお待ちください。
        </div>
        <meta http-equiv="refresh" content="0;url=?step=install">
      <?php endif ?>

    <!-- STEP 5: 完了 -->
    <?php elseif ($step === 'complete'): ?>
      <h3 class="card-title text-success">インストール完了</h3>
      <p>ci4transfer のインストールが完了しました。</p>
      <div class="alert alert-warning">
        <strong>セキュリティ上の注意：</strong><br>
        <code>public/install/</code> ディレクトリを必ず削除してください。
        <pre class="mt-2 mb-0">rm -rf public/install/</pre>
      </div>
      <a href="../../" class="btn btn-primary mt-2">サイトへ</a>
      <a href="../../admin" class="btn btn-outline-secondary mt-2">管理画面へ</a>
    <?php endif ?>

    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>
</body>
</html>
