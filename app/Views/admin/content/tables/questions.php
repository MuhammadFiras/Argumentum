<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<div class="card mb-4">
  <div class="card-header">
    <i class="fas fa-table me-1"></i>
    Questions Table
  </div>
  <div class="card-body">
    <table id="datatablesSimple">
      <thead>
        <tr>
          <th>Question ID</th>
          <th>User ID</th>
          <th>Title</th>
          <th>Content</th>
          <th>Slug</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Question ID</th>
          <th>User ID</th>
          <th>Title</th>
          <th>Content</th>
          <th>Slug</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </tfoot>
      <tbody>
        <?php if (!empty($questions) && is_array($questions)): ?>
          <?php foreach ($questions as $question): ?>
            <tr>
              <td><?= esc($question['id_question']); ?></td>
              <td><?= esc($question['id_user']); ?></td>
              <td><?= esc($question['title']); ?></td>
              <td><?= esc($question['content']); ?></td>
              <td><?= esc($question['slug']); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($question['created_at'])->toLocalizedString('d MMM yyyy'); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($question['updated_at'])->toLocalizedString('d MMM yyyy'); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection(); ?>