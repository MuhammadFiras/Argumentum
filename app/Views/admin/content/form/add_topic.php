<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
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
    <!-- .... -->
  </div>
</div>
<?= $this->endSection(); ?>