<?= $this->extend('layout/main_layout') ?>

<?= $this->section('content') ?>
<div class="container" data-aos="fade-up">
    <div class="form-container">
        <h2>Edit Pertanyaan</h2>
        <hr>

        <?php if (!empty($question)): ?>
            <form action="<?= site_url('questions/update/' . $question['id_question']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Pertanyaan</label>
                    <input
                        type="text"
                        class="form-control <?= $validation->hasError('title') ? 'is-invalid' : '' ?>"
                        id="title"
                        name="title"
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
                    <textarea
                        class="form-control <?= $validation->hasError('content') ? 'is-invalid' : '' ?>"
                        id="content"
                        name="content"
                        rows="5"
                        placeholder="Tambahkan detail atau konteks untuk pertanyaanmu..."><?= old('content', esc($question['content'])) ?></textarea>
                    <?php if ($validation->hasError('content')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('content') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- <div class="mb-3">
                    <p for="topics" class="form-label">Topik</p>
                    <div class="row">
                        <div class="col">
                            <input type="checkbox" name="Teknologi" id="Teknologi" value="teknologi">
                            <label for="Teknologi">Teknologi</label>
                        </div>
                        <div class="col">
                            <input type="checkbox" name="Kesehatan" id="Kesehatan" value="kesehatan">
                            <label for="Kesehatan">Kesehatan</label>
                        </div>
                        <div class="col">
                            <input type="checkbox" name="Olahraga" id="Olahraga" value="olahraga">
                            <label for="Olahraga">Olahraga</label>
                        </div>
                        <div class="col">
                            <input type="checkbox" name="Politik" id="Politik" value="politik">
                            <label for="Politik">Politik</label>
                        </div>
                    </div>
                </div> -->

                <div class="mb-3">
                    <label for="topics" class="form-label">Topik (Pilih minimal satu)</label>
                    <div class="row p-2 border rounded <?= $validation->hasError('topics') ? 'is-invalid' : '' ?>">

                        <?php if (!empty($all_topics)): ?>
                            <?php foreach ($all_topics as $topic): ?>
                                <?php
                                // Logika untuk menentukan apakah checkbox harus dicentang
                                $isChecked = false;
                                // Prioritas 1: Cek apakah ada data 'old' dari form (jika validasi gagal)
                                if (old('topics')) {
                                    $isChecked = in_array($topic['id'], old('topics'));
                                }
                                // Prioritas 2: Jika tidak ada 'old' data, cek data dari database
                                else if (!empty($existing_topics)) {
                                    $isChecked = in_array($topic['id'], $existing_topics);
                                }
                                ?>
                                <div class="col-md-3 col-6">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="topics[]"
                                            value="<?= esc($topic['id']) ?>"
                                            id="topic-<?= esc($topic['id']) ?>"
                                            <?= $isChecked ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="topic-<?= esc($topic['id']) ?>">
                                            <?= esc($topic['name']) ?>
                                        </label>
                                    </div>
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
                    <a href="<?= site_url('question/' . $question['slug']) ?>" class="btn btn-link">Batal</a>
                    <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">Pertanyaan tidak ditemukan atau Anda tidak memiliki akses.</div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>