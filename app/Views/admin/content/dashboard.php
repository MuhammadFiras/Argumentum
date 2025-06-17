<?= $this->extend('admin/admin_layout.php'); ?>

<?= $this->section('content'); ?>
<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4 mb-5">Dashboard</h1>
    <div class="row">
      <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
          <div class="card-header">Total User</div>
          <div class="card-body fs-2 fw-bold"><?= $count['user']; ?></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white mb-4">
          <div class="card-header">Total Question</div>
          <div class="card-body fs-2 fw-bold"><?= $count['question']; ?></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
          <div class="card-header">Total Answer</div>
          <div class="card-body fs-2 fw-bold"><?= $count['answer']; ?></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white mb-4">
          <div class="card-header">Total Comment</div>
          <div class="card-body fs-2 fw-bold"><?= $count['comment']; ?></div>
        </div>
      </div>
    </div>
  </div>
</main>
<?= $this->endSection(); ?>