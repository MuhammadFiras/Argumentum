<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <title><?= esc($title ?? 'Profil Pengguna') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <style>
        body { background-color: #f0f2f5; font-family: sans-serif; }
        .profile-header { background-color: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .profile-picture { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .profile-name { font-size: 2rem; font-weight: bold; }
        .profile-credentials { color: #606770; font-size: 1.1rem; }
        .profile-description { margin-top: 1rem; color: #1c1e21; line-height: 1.6; }
        .social-links a { color:rgb(16, 118, 186); margin-right: 15px; font-size: 1.5rem; }
        .social-links a:hover { color: #8c211e; }
        .content-section { margin-top: 2rem; background-color: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .content-section h5 { font-weight: bold; margin-bottom: 1rem; color: #B92B27; }
        .list-item { border-bottom: 1px solid #e9ebee; padding-bottom: 0.75rem; margin-bottom: 0.75rem; }
        .list-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .list-item-title a { text-decoration: none; color: #0d6efd; font-weight: 500; }
        .list-item-title a:hover { text-decoration: underline; }
        .list-item-content { font-size: 0.95rem; color: #4b4f56; margin-top: 0.25rem; }
        .navbar-brand { font-weight: bold; color: #B92B27 !important; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">Argumentum</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Beranda</a></li>
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('/ask') ?>">Tanya Pertanyaan</a></li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= esc(session()->get('nama_lengkap')) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="<?= site_url('/profile') ?>">Profil Saya</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('/profile/edit') ?>">Edit Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= site_url('/logout') ?>">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= site_url('/login') ?>" class="btn btn-outline-primary me-2">Masuk</a>
                        <a href="<?= site_url('auth/register') ?>" class="btn btn-primary">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (!empty($user_profile)): ?>
            <div class="profile-header text-center text-md-start">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <?php
                            $photo_url = base_url('default.jpg'); // Default jika tidak ada foto atau fotonya 'default.jpg'
                            if (!empty($user_profile['photo_profile']) && $user_profile['photo_profile'] != 'default.jpg') {
                                // Jika ada foto profil spesifik dan BUKAN 'default.jpg'
                                $specific_photo_path = 'assets/images/profiles/' . esc($user_profile['photo_profile']);
                                // Cek apakah file foto spesifik ada, jika tidak, fallback ke default.jpg di root public
                                // Pengecekan file existence sebaiknya tidak dilakukan di view karena performa,
                                // tapi untuk kasus path, kita asumsikan path ini benar jika photo_profile diisi dengan benar.
                                // Jika kamu ingin lebih aman, pengecekan file bisa dilakukan di controller atau dengan helper.
                                // Untuk sekarang, kita asumsikan jika photo_profile ada dan bukan 'default.jpg', filenya ada di path tersebut.
                                $photo_url = base_url($specific_photo_path);
                            }
                        ?>
                        <img src="<?= $photo_url ?>" alt="Foto Profil <?= esc($user_profile['nama_lengkap']) ?>" class="profile-picture">
                    </div>
                    <div class="col-md-9">
                        <h1 class="profile-name"><?= esc($user_profile['nama_lengkap']) ?></h1>
                        <?php if (!empty($user_profile['credentials'])): ?>
                            <p class="profile-credentials"><?= esc($user_profile['credentials']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($user_profile['description'])): ?>
                            <p class="profile-description"><?= nl2br(esc($user_profile['description'])) ?></p>
                        <?php endif; ?>
                        <div class="social-links mt-3">
                            <?php if (!empty($user_profile['linkedin_url'])): ?>
                                <a href="<?= esc($user_profile['linkedin_url'], 'attr') ?>" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($user_profile['instagram_url'])): ?>
                                <a href="<?= esc($user_profile['instagram_url'], 'attr') ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                        </div>
                        <?php if ($is_own_profile): ?>
                            <a href="<?= site_url('/profile/edit') ?>" class="btn btn-outline-primary btn-sm mt-3">Edit Profil</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <ul class="nav nav-tabs mb-3" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions-content" type="button" role="tab" aria-controls="questions-content" aria-selected="true">Pertanyaan (<?= count($questions_by_user) ?>)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="answers-tab" data-bs-toggle="tab" data-bs-target="#answers-content" type="button" role="tab" aria-controls="answers-content" aria-selected="false">Jawaban (<?= count($answers_by_user) ?>)</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="profileTabContent">
                        <div class="tab-pane fade show active content-section" id="questions-content" role="tabpanel" aria-labelledby="questions-tab">
                            <h5>Pertanyaan Dibuat</h5>
                            <?php if (!empty($questions_by_user)): ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($questions_by_user as $question): ?>
                                        <li class="list-item">
                                            <div class="list-item-title">
                                                <a href="<?= site_url('question/' . esc($question['slug'])) ?>"><?= esc($question['title']) ?></a>
                                            </div>
                                            <small class="text-muted">Dibuat: <?= CodeIgniter\I18n\Time::parse($question['created_at'])->toLocalizedString('d MMMM yyyy') ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted"><?= ($is_own_profile) ? 'Anda belum membuat pertanyaan.' : 'Pengguna ini belum membuat pertanyaan.' ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="tab-pane fade content-section" id="answers-content" role="tabpanel" aria-labelledby="answers-tab">
                            <h5>Jawaban Diberikan</h5>
                            <?php if (!empty($answers_by_user)): ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($answers_by_user as $answer): ?>
                                        <li class="list-item">
                                            <div class="list-item-title">
                                                <a href="<?= site_url('question/' . esc($answer['question_slug']) . '#answer-' . $answer['id_answer']) ?>">
                                                    Jawaban untuk: <?= esc($answer['question_title']) ?>
                                                </a>
                                            </div>
                                            <p class="list-item-content"><?= word_limiter(esc($answer['content']), 30) ?></p>
                                            <small class="text-muted">Diberikan: <?= CodeIgniter\I18n\Time::parse($answer['created_at'])->toLocalizedString('d MMMM yyyy') ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted"><?= ($is_own_profile) ? 'Anda belum memberikan jawaban.' : 'Pengguna ini belum memberikan jawaban.' ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <p class="alert alert-warning">Profil pengguna tidak dapat ditemukan.</p>
        <?php endif; ?>
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        © Argumentum, <?= date('Y') ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>