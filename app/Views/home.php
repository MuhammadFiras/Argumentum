<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Argumentum') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; color: #B92B27 !important; }
        .question-card { margin-bottom: 20px; }
        .question-title a { text-decoration: none; color: #212529; }
        .question-title a:hover { color: #B92B27; }
        .user-info { font-size: 0.9em; color: #6c757d; }
        .ask-button { background-color: #B92B27; border-color: #B92B27; color:white; }
        .ask-button:hover { background-color: #a32622; border-color: #a32622; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">Argumentum</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= site_url('/') ?>">Beranda</a>
                    </li>
                    </ul>
                <div class="d-flex">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <a href="<?= site_url('/ask') ?>" class="btn ask-button me-2">Tanya Pertanyaan</a>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= esc(session()->get('nama_lengkap')) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                                <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= site_url('/logout') ?>">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= site_url('/login') ?>" class="btn btn-outline-primary me-2">Masuk</a>
                        <a href="<?= site_url('/register') ?>" class="btn btn-primary">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
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

        <div class="d-flex justify-content-between align-items-center mb-3">
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
                            <?= word_limiter(esc($question['content']), 30) // Tampilkan sebagian konten ?>
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

    <footer class="text-center mt-5 mb-3 text-muted">
        © Argumentum, <?= date('Y') ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>