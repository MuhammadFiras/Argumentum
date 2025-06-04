<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Detail Pertanyaan') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; color: #B92B27 !important; }
        .question-detail-container { max-width: 800px; margin: 30px auto; }
        .question-header { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom:20px;}
        .question-content { font-size: 1.1em; line-height: 1.7; }
        .answer-card { background-color: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom:15px; }
        .user-info { font-size: 0.9em; color: #6c757d; }
        .btn-submit-answer { background-color: #B92B27; border-color: #B92B27; color:white; }
        .btn-submit-answer:hover { background-color: #a32622; border-color: #a32622; }
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
                        <a class="nav-link" href="<?= site_url('/') ?>">Beranda</a>
                    </li>
                     <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item">
                         <a class="nav-link" href="<?= site_url('/ask') ?>">Tanya Pertanyaan</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= esc(session()->get('nama_lengkap')) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('/logout') ?>">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= site_url('/login') ?>" class="btn btn-outline-primary me-2">Masuk</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container question-detail-container">
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

        <?php if (!empty($question)): ?>
            <div class="question-header">
                <h1><?= esc($question['title']) ?></h1>
                <div class="user-info mb-2">
                    <img src="<?= base_url('assets/images/profiles/' . esc($question['user_photo'] ?? 'default.jpg')) ?>" alt="<?= esc($question['user_nama']) ?>" width="30" height="30" class="rounded-circle me-1">
                    Ditanyakan oleh: <?= esc($question['user_nama']) ?> · <?= CodeIgniter\I18n\Time::parse($question['created_at'])->humanize() ?>
                </div>
                <hr>
                <div class="question-content">
                    <?= nl2br(esc($question['content'])) // nl2br untuk mengubah newline jadi <br> ?>
                </div>
                <?php if (session()->get('isLoggedIn') && session()->get('user_id') == $question['id_user']): ?>
                    <div class="mt-3">
                        <a href="<?= site_url('questions/edit/' . $question['id_question']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="<?= site_url('questions/delete/' . $question['id_question']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">Hapus</a>
                    </div>
                <?php endif; ?>
            </div>

            <h4 class="mt-4 mb-3"><?= count($answers) ?> Jawaban</h4>

            <?php if (session()->get('isLoggedIn')): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tulis Jawaban Anda</h5>
                    <form action="<?= site_url('answer/submit/' . $question['id_question']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <textarea class="form-control" name="answer_content" rows="4" placeholder="Tulis jawabanmu di sini..." required></textarea>
                             <?php
                                $validation = session()->getFlashdata('validation_answer');
                                if ($validation && $validation->hasError('answer_content')):
                            ?>
                                <div class="invalid-feedback d-block">
                                    <?= $validation->getError('answer_content') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-submit-answer">Kirim Jawaban</button>
                    </form>
                </div>
            </div>
            <?php else: ?>
                <p><a href="<?= site_url('login?redirect=' . current_url()) ?>">Masuk</a> untuk menjawab pertanyaan ini.</p>
            <?php endif; ?>


            <?php if (!empty($answers)): ?>
                <?php foreach ($answers as $answer): ?>
                    <div class="answer-card">
                        <div class="user-info mb-2">
                             <img src="<?= base_url('assets/images/profiles/' . esc($answer['user_photo'] ?? 'default.jpg')) ?>" alt="<?= esc($answer['user_nama']) ?>" width="25" height="25" class="rounded-circle me-1">
                            <?= esc($answer['user_nama']) ?> · <?= CodeIgniter\I18n\Time::parse($answer['created_at'])->humanize() ?>
                        </div>
                        <div class="answer-content">
                            <?= nl2br(esc($answer['content'])) ?>
                        </div>
                         </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada jawaban untuk pertanyaan ini. Jadilah yang pertama menjawab!</p>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-warning" role="alert">
              Pertanyaan tidak ditemukan.
            </div>
        <?php endif; ?>
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        © Argumentum, <?= date('Y') ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>