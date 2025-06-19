<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<?php $validation = session()->getFlashdata('validation') ?? \Config\Services::validation(); ?>
<div class="card mb-4">
  <div class="card-header">
    <i class="fa-solid fa-newspaper me-1"></i>
    Add Topic
  </div>

  <div class="card-body">
    <div class="my-2">
      <form action="<?= site_url('/admin/form/topics-insert'); ?>" method="post" class="row g-3 mb-2">
        <?= csrf_field(); ?>
        <div class="col-auto">
          <label for="newTopic" class="col-form-label">Topik Baru</label>
        </div>
        <div class="col-auto">
          <input type="text" class="form-control <?= $validation->hasError('newTopic') ? 'is-invalid' : '' ?>" id="newTopic" name="newTopic" value="<?= old('newTopic'); ?>" required>
          <div class="invalid-feedback">
            <?= $validation->getError('newTopic') ?>
          </div>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary mb-3">Tambah</button>
        </div>
      </form>

      <a href="<?= site_url('admin/tables/topics'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </div>
</div>
<?= $this->endSection(); ?>