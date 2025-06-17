<?= $this->extend('admin/admin_layout'); ?>

<?= $this->section('content'); ?>
<div class="card mb-4">
  <div class="card-header">
    <i class="fas fa-table me-1"></i>
    Answer Ratings Table
  </div>
  <div class="card-body">
    <table id="datatablesSimple">
      <thead>
        <tr>
          <th>Rating ID</th>
          <th>Answer ID</th>
          <th>User ID</th>
          <th>Rating</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Rating ID</th>
          <th>Answer ID</th>
          <th>User ID</th>
          <th>Rating</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </tfoot>
      <tbody>
        <?php if (!empty($ratings) && is_array($ratings)): ?>
          <?php foreach ($ratings as $rating): ?>
            <tr>
              <td><?= esc($rating['id_rating']) ?></td>
              <td><?= esc($rating['id_answer']); ?></td>
              <td><?= esc($rating['id_user']); ?></td>
              <td><?= esc($rating['rating']); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($rating['created_at'])->toLocalizedString('d MMM yyyy'); ?></td>
              <td><?= CodeIgniter\I18n\Time::parse($rating['updated_at'])->toLocalizedString('d MMM yyyy'); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection(); ?>