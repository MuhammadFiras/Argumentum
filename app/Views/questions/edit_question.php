<?= $this->extend('layout/main_layout') ?>

<?= $this->section('title') ?>
    <?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="form-container">
        <h2>Edit Pertanyaan</h2>
        <hr>

        <?php if (!empty($question)): ?>
            <form action="<?= site_url('questions/update/' . $question['id_question']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Pertanyaan</label>
                    <input type="text" class="form-control <?= $validation->hasError('title') ? 'is-invalid' : '' ?>"
                           id="title" name="title"
                           value="<?= old('title', esc($question['title'])) ?>"
                           placeholder="Mulai pertanyaanmu dengan 'Apa', 'Bagaimana', 'Mengapa', dll.">
                    <?php if ($validation->hasError('title')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('title') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Detail Pertanyaan</label>
                    <textarea class="form-control <?= $validation->hasError('content') ? 'is-invalid' : '' ?>"
                              id="content" name="content" rows="5"
                              placeholder="Tambahkan detail atau konteks untuk pertanyaanmu..."><?= old('content', esc($question['content'])) ?></textarea>
                    <?php if ($validation->hasError('content')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('content') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
                <a href="<?= site_url('question/' . $question['slug']) ?>" class="btn btn-link">Batal</a>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">Pertanyaan tidak ditemukan atau Anda tidak memiliki akses.</div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>