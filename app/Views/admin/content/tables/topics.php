<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<div class="card mb-4">
  <div class="card-header">
    <i class="fas fa-table me-1"></i>
    Topics Table
  </div>
  <div class="card-body">
    <table id="datatablesSimple">
      <thead>
        <tr>
          <th>Topic ID</th>
          <th>Name</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Topic ID</th>
          <th>Name</th>
        </tr>
      </tfoot>
      <tbody>
        <?php if (!empty($topics) && is_array($topics)): ?>
          <?php foreach ($topics as $topic): ?>
            <tr>
              <td><?= esc($topic['id']); ?></td>
              <td><?= esc($topic['name']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection(); ?>