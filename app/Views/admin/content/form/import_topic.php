<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<?php $validation = session()->getFlashdata('validation') ?? \Config\Services::validation(); ?>
<div class="card mb-4">
  <div class="card-header">
    <i class="fa-solid fa-newspaper me-1"></i>
    Import Topic
  </div>

  <div class="card-body">
    <div class="my-2">
      <form action="<?= site_url('/admin/form/topics/import'); ?>" method="post" enctype="multipart/form-data" class="row g-3 mb-2">
        <?= csrf_field(); ?>
        <div class="col-auto">
          <label for="excel_file" class="col-form-label">Upload Excel File</label>
        </div>
        <div class="col-auto">
          <input type="file" class="form-control <?= $validation->hasError('excel_file') ? 'is-invalid' : '' ?>" id="excel_file" name="excel_file" value="<?= old('excel_file'); ?>" required>
          <div class="invalid-feedback">
            <?= $validation->getError('excel_file') ?>
          </div>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary mb-3">Import</button>
        </div>
      </form>

      <a href="<?= site_url('admin/tables/topics'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </div>
</div>
<?= $this->endSection(); ?>