<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>

<div class="card mb-4">
  <div class="card-header">
    <i class="fas fa-table me-1"></i>
    Users Table
  </div>
  <div class="card-body">
    <table id="datatablesSimple">
      <thead>
        <tr>
          <th>User ID</th>
          <th>Nama Lengkap</th>
          <th>Email</th>
          <th>Password (Hash)</th>
          <th>Role</th>
          <th>Photo Profile</th>
          <th>Description</th>
          <th>Credentials</th>
          <th>LinkedIn</th>
          <th>Instagram</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>User ID</th>
          <th>Nama Lengkap</th>
          <th>Email</th>
          <th>Password (Hash)</th>
          <th>Role</th>
          <th>Photo Profile</th>
          <th>Description</th>
          <th>Credentials</th>
          <th>LinkedIn</th>
          <th>Instagram</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </tfoot>
      <tbody>
        <?php if (!empty($users) && is_array($users)): ?>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= esc($user['id_user']); ?></td>
              <td><?= esc($user['nama_lengkap']); ?></td>
              <td><?= esc($user['email']); ?></td>
              <td>
                <span class="text-muted fst-italic">Hidden</span>
              </td>
              <td>
                <?php if ($user['role'] == 'admin'): ?>
                  <span class="badge bg-danger"><?= esc($user['role']); ?></span>
                <?php else: ?>
                  <span class="badge bg-secondary"><?= esc($user['role']); ?></span>
                <?php endif; ?>
              </td>
              <td><?= esc($user['photo_profile']); ?></td>
              <td><?= esc($user['description']); ?></td>
              <td><?= esc($user['credentials']); ?></td>
              <td><?= esc($user['linkedin_url']); ?></td>
              <td><?= esc($user['instagram_url']); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($user['created_at'])->toLocalizedString('d MMM yyyy'); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($user['updated_at'])->toLocalizedString('d MMM yyyy'); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection(); ?>