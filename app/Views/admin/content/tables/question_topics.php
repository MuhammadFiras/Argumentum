<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<div class="card mb-4">
  <div class="card-header">
    <i class="fas fa-table me-1"></i>
    Questions Topics Table
  </div>
  <div class="card-body">
    <table id="datatablesSimple">
      <thead>
        <tr>
          <th>Question ID</th>
          <th>Topic ID</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Question ID</th>
          <th>Topic ID</th>
        </tr>
      </tfoot>
      <tbody>
        <?php if (!empty($pivots) && is_array($pivots)): ?>
          <?php foreach ($pivots as $pivot): ?>
            <tr>
              <td><?= esc($pivot['question_id']); ?></td>
              <td><?= esc($pivot['topic_id']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection(); ?>