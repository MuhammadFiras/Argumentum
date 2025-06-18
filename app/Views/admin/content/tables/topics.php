<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<div class="card mb-4">
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible m-2" role="alert">
      <?= session()->getFlashdata('success'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible m-2" role="alert">
      <?= session()->getFlashdata('error'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif ?>

  <div class="card-header d-flex justify-content-between align-items-center">
    <span>
      <i class="fas fa-table me-1"></i>
      Topics Table
    </span>
    <a href="<?= site_url('/admin/form/add-topics') ?>" class="btn btn-primary btn-sm">
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
                  <a class="btn btn-sm btn-primary me-2" href="<?= site_url('/admin/form/edit-topics/' . esc($topic['id'])); ?>">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="<?= site_url('/admin/form/topics-delete/' . esc($topic['id'])) ?>" method="post" class="d-inline">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><i class="fas fa-trash"></i></button>
                  </form>
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