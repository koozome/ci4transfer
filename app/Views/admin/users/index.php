<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="page-header mb-3">
  <div class="row align-items-center">
    <div class="col">
      <h2 class="page-title"><?= esc($pageTitle) ?></h2>
    </div>
    <div class="col-auto">
      <a href="<?= site_url('admin/users/add') ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
        ユーザー追加
      </a>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-vcenter card-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>メールアドレス</th>
          <th>グループ</th>
          <th>登録日時</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
          <tr><td colspan="5" class="text-center text-secondary">データなし</td></tr>
        <?php else: ?>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= esc($user->id) ?></td>
              <td><?= esc($user->email) ?></td>
              <td>
                <?php foreach ($user->getGroups() as $group): ?>
                  <span class="badge <?= $group === 'administrator' ? 'bg-red' : 'bg-blue' ?>">
                    <?= esc($group) ?>
                  </span>
                <?php endforeach ?>
              </td>
              <td><?= esc($user->created_at) ?></td>
              <td class="text-end">
                <a href="<?= site_url('admin/users/edit/' . $user->id) ?>" class="btn btn-sm btn-outline-secondary">編集</a>
                <a href="<?= site_url('admin/users/password/' . $user->id) ?>" class="btn btn-sm btn-outline-secondary">PW変更</a>
                <?php if ($user->id !== auth()->id()): ?>
                  <form method="post" action="<?= site_url('admin/users/delete/' . $user->id) ?>" class="d-inline" onsubmit="return confirm('削除しますか？')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                  </form>
                <?php endif ?>
              </td>
            </tr>
          <?php endforeach ?>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
