<?= $this->extend('layout/main_layout') ?>

<?= $this->section('content') ?>
<div class="container" data-aos="fade-up">
    <div class="form-container">
        <h2>Tanya Pertanyaan Baru</h2>
        <hr>

        <?php $validation = session()->getFlashdata('validation') ?? \Config\Services::validation(); ?>

        <form action="<?= site_url('questions/create') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="title" class="form-label">Judul Pertanyaan</label>
                <input
                    type="text"
                    class="form-control <?= $validation->hasError('title') ? 'is-invalid' : '' ?>"
                    id="title"
                    name="title"
                    value="<?= old('title') ?>"
                    placeholder="Mulai pertanyaanmu dengan 'Apa', 'Bagaimana', 'Mengapa', dll.">
                <?php if ($validation->hasError('title')): ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('title') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Detail Pertanyaan</label>
                <textarea
                    class="form-control <?= $validation->hasError('content') ? 'is-invalid' : '' ?>"
                    id="content"
                    name="content"
                    rows="5"
                    placeholder="Tambahkan detail atau konteks untuk pertanyaanmu..."><?= old('content') ?></textarea>
                <?php if ($validation->hasError('content')): ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('content') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="topics" class="form-label">Topik (Pilih minimal satu)</label>
                <div class="p-2 border rounded <?= $validation->hasError('topics') ? 'is-invalid' : '' ?>">
                    <?php if (!empty($topics)): ?>
                        <?php foreach ($topics as $topic): ?>

                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="topics[]"
                                    value="<?= esc($topic['id']) ?>"
                                    id="topic-<?= esc($topic['id']) ?>"
                                    <?= old('topics') && in_array($topic['id'], old('topics')) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="topic-<?= esc($topic['id']) ?>">
                                    <?= esc($topic['name']) ?>
                                </label>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada topik tersedia.</p>
                    <?php endif; ?>
                </div>
                <?php if ($validation->hasError('topics')): ?>
                    <div class="invalid-feedback d-block">
                        <?= $validation->getError('topics') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="<?= site_url('/') ?>" class="btn btn-link">Batal</a>
                <button type="submit" class="btn btn-submit">Publikasikan Pertanyaan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>