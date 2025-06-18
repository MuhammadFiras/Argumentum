<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>
      <i class="fas fa-table me-1"></i>
      Topics Table
    </span>
    <a href="<?= site_url('admin/users/new') ?>" class="btn btn-primary btn-sm">
      <i class="fas fa-plus"></i> Tambah Data
    </a>
  </div>

  <div class="card-body">
    <table id="datatablesSimple">
      <thead>
        <tr>
          <th>Topic ID</th>
          <th>Name</th>
          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Topic ID</th>
          <th>Name</th>
          <th>Action</th>
        </tr>
      </tfoot>
      <tbody>
        <?php if (!empty($topics) && is_array($topics)): ?>
          <?php foreach ($topics as $topic): ?>
            <tr>
              <td><?= esc($topic['id']); ?></td>
              <td><?= esc($topic['name']); ?></td>
              <td>
                <div class="d-flex justify-content-center">
                  <button class="btn btn-sm btn-primary me-2">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection(); ?>