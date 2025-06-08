<?= $this->extend('layout/main_layout') ?>

<?= $this->section('title') ?>
    <?= esc($title) ?>
<?= $this->endSection() ?>

<?php // Konten utama halaman ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="title-home d-flex justify-content-between align-items-center mb-3" >
        <h1>Daftar Pertanyaan</h1>
    </div>

    <?php if (!empty($questions) && is_array($questions)): ?>
        <?php foreach ($questions as $question): ?>
            <div class="card question-card">
                <div class="card-body">
                    <h5 class="card-title question-title">
                        <a href="<?= site_url('question/' . esc($question['slug'], 'url')) ?>">
                            <?= esc($question['title']) ?>
                        </a>
                    </h5>
                    <p class="card-text">
                        <?= word_limiter(esc($question['content']), 30) ?>
                    </p>
                    <div class="user-info">
                        Ditanyakan oleh: <?= esc($question['user_nama']) ?>
                        pada <?= CodeIgniter\I18n\Time::parse($question['created_at'])->humanize() ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">Belum ada pertanyaan.</p>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>