<?= $this->extend('layout/main_layout') ?>

<?= $this->section('title') ?>
    <?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-down">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-down">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-right">
        <h1 class="text-danger fw-bold">Daftar Pertanyaan</h1>
        <?php if (session()->get('isLoggedIn')): ?>
            <a href="<?= site_url('/ask') ?>" class="btn btn-danger btn-sm shadow-sm">+ Tanya Pertanyaan</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($questions) && is_array($questions)): ?>
        <div class="row g-4">
            <?php foreach ($questions as $question): ?>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?= rand(0, 300) ?>">
                    <div class="card question-card shadow-sm border-0 rounded-3">
                        <div class="card-body">
                            <h5 class="card-title question-title">
                                <a href="<?= site_url('question/' . esc($question['slug'], 'url')) ?>" class="text-decoration-none text-dark fw-semibold">
                                    <?= esc($question['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-secondary">
                                <?= word_limiter(esc($question['content']), 30) ?>
                            </p>
                            <div class="user-info text-muted fst-italic small">
                                Ditanyakan oleh: <?= esc($question['user_nama']) ?> <br>
                                <small><?= CodeIgniter\I18n\Time::parse($question['created_at'])->humanize() ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center fst-italic text-secondary" data-aos="fade-in">Belum ada pertanyaan.</p>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
