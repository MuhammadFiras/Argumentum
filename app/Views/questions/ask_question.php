<?= $this->extend('layout/main_layout') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

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
                <label for="content" class="form-label">Detail Pertanyaan (Opsional)</label>
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

            <div class="d-flex justify-content-end mt-4">
                <a href="<?= site_url('/') ?>" class="btn btn-link">Batal</a>
                <button type="submit" class="btn btn-submit">Publikasikan Pertanyaan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>