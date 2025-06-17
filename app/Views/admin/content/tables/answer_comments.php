<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<div class="card mb-4">
  <div class="card-header">
    <i class="fas fa-table me-1"></i>
    Answer Comments Table
  </div>
  <div class="card-body">
    <table id="datatablesSimple">
      <thead>
        <tr>
          <th>Comment ID</th>
          <th>Answer ID</th>
          <th>User ID</th>
          <th>Comment Text</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Comment ID</th>
          <th>Answer ID</th>
          <th>User ID</th>
          <th>Comment Text</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </tfoot>
      <tbody>
        <?php if (!empty($answerComments) && is_array($answerComments)): ?>
          <?php foreach ($answerComments as $comment): ?>
            <tr>
              <td><?= esc($comment['id_comment']); ?></td>
              <td><?= esc($comment['id_answer']); ?></td>
              <td><?= esc($comment['id_user']); ?></td>
              <td><?= esc($comment['comment_text']); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($comment['created_at'])->toLocalizedString('d MMM yyyy'); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($comment['updated_at'])->toLocalizedString('d MMM yyyy'); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection(); ?>