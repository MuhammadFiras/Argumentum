<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<div class="card mb-4">
  <div class="card-header">
    <i class="fas fa-table me-1"></i>
    Answers Table
  </div>
  <div class="card-body">
    <table id="datatablesSimple">
      <thead>
        <tr>
          <th>Answer ID</th>
          <th>Question ID</th>
          <th>User ID</th>
          <th>Content</th>
          <th>Is Best Answer</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Answer ID</th>
          <th>Question ID</th>
          <th>User ID</th>
          <th>Content</th>
          <th>Is Best Answer</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </tfoot>
      <tbody>
        <?php if (!empty($answers) && is_array($answers)): ?>
          <?php foreach ($answers as $answer): ?>
            <tr>
              <td><?= esc($answer['id_answer']); ?></td>
              <td><?= esc($answer['id_question']); ?></td>
              <td><?= esc($answer['id_user']); ?></td>
              <td><?= esc($answer['content']); ?></td>
              <td><?= esc($answer['is_best_answer']); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($answer['created_at'])->toLocalizedString('d MMM yyyy'); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($answer['updated_at'])->toLocalizedString('d MMM yyyy'); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection(); ?>