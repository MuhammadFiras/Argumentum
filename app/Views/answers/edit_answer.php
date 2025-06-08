<?= $this->extend('layout/main_layout') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="form-container">
        <h2><?= esc($title) ?></h2>
        <hr>

        <?php if (!empty($question)): ?>
            <div class="original-question-preview">
                <strong>Pertanyaan Asli:</strong> <?= esc($question['title']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($answer)): ?>
            <form action="<?= site_url('answer/update/' . $answer['id_answer']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">

                <div class="mb-3">
                    <label for="answer_content" class="form-label">Edit Jawaban Anda:</label>
                    <textarea class="form-control <?= $validation->hasError('answer_content') ? 'is-invalid' : '' ?>"
                        id="answer_content" name="answer_content" rows="8"><?= old('answer_content', esc($answer['content'])) ?></textarea>
                    <?php if ($validation->hasError('answer_content')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('answer_content') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
                <?php if ($question): ?>
                    <a href="<?= site_url('question/' . $question['slug'] . '#answer-' . $answer['id_answer']) ?>" class="btn btn-link">Batal</a>
                <?php else: ?>
                    <a href="<?= site_url('/') ?>" class="btn btn-link">Batal</a>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">Jawaban tidak ditemukan atau Anda tidak memiliki akses.</div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>