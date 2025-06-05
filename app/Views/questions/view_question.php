<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <title><?= esc($title ?? 'Detail Pertanyaan') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; color: #B92B27 !important; }
        .question-detail-container { max-width: 800px; margin: 30px auto; }
        .question-header { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom:20px;}
        .question-content { font-size: 1.1em; line-height: 1.7; }
        .answer-card {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom:15px;
            border: 1px solid #dee2e6; /* Default border */
        }
        .user-info { font-size: 0.9em; color: #6c757d; }
        .btn-submit-answer { background-color: #B92B27; border-color: #B92B27; color:white; }
        .btn-submit-answer:hover { background-color: #a32622; border-color: #a32622; }

        /* CSS untuk Bintang Rating */
        .star-rating .star {
            font-size: 1.5em;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
            margin-right: 2px;
        }
        .star-rating .star:hover,
        .star-rating .star.rated {
            color: #ffc107;
        }
        .rating-summary-text {
            font-size: 0.9em;
            color: #6c757d;
        }
        .rating-feedback-message {
            font-size: 0.8em;
            min-height: 1.2em;
            margin-top: 4px;
        }
        .text-info { color: #0dcaf0 !important; }
        .text-success { color: #198754 !important; }
        .text-danger { color: #dc3545 !important; }

        /* CSS untuk Jawaban Terbaik */
        .best-answer-badge {
            font-size: 0.8em;
            font-weight: bold;
            padding: 0.3em 0.6em;
            border-radius: 0.25rem;
            background-color: #198754; /* Warna hijau sukses Bootstrap */
            color: white;
            margin-left: 8px;
            vertical-align: middle;
        }
        .best-answer-card {
            border: 2px solid #198754; /* Border hijau pada card jawaban terbaik */
            background-color: #e6f7f0; /* Warna latar sedikit hijau untuk jawaban terbaik */
        }
        .toggle-best-answer-btn { /* Untuk tombol di dalam form */
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
        }
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
                                <li><a class="dropdown-item" href="<?= site_url('/profile') ?>">Profil Saya</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('/profile') ?>">Edit Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= site_url('/logout') ?>">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= site_url('/login') ?>" class="btn btn-outline-primary me-2">Masuk</a>
                        <a href="<?= site_url('auth/register') ?>" class="btn btn-primary">Daftar</a> <?php endif; ?>
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
                    <?= nl2br(esc($question['content'])) ?>
                </div>
                <?php if (session()->get('isLoggedIn')): ?>
                    <?php
                        $isOwner = (session()->get('user_id') == $question['id_user']);
                        $isAdmin = (session()->get('role') == 'admin');
                    ?>
                    <div class="mt-3">
                        <?php if ($isOwner): ?>
                            <a href="<?= site_url('questions/edit/' . $question['id_question']) ?>" class="btn btn-sm btn-outline-secondary">Edit Pertanyaan</a>
                        <?php endif; ?>

                        <?php if ($isOwner || $isAdmin): // Tombol hapus muncul untuk pemilik ATAU admin ?>
                            <form action="<?= site_url('questions/delete/' . $question['id_question']) ?>" method="post" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?<?= $isAdmin && !$isOwner ? " (Sebagai Admin)" : "" ?>');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus Pertanyaan</button>
                            </form>
                        <?php endif; ?>
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
                            <textarea class="form-control <?= ($validation_answer->hasError('answer_content')) ? 'is-invalid' : '' ?>" name="answer_content" rows="4" placeholder="Tulis jawabanmu di sini..." required><?= old('answer_content') ?></textarea>
                            <?php if ($validation_answer->hasError('answer_content')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $validation_answer->getError('answer_content') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-submit-answer">Kirim Jawaban</button>
                    </form>
                </div>
            </div>
            <?php else: ?>
                <p><a href="<?= site_url('login?redirect=' . urlencode(current_url())) ?>">Masuk</a> untuk menjawab pertanyaan ini.</p>
            <?php endif; ?>


            <?php if (!empty($answers)): ?>
                <?php foreach ($answers as $answer): ?>
                    <div class="answer-card <?= $answer['is_best_answer'] ? 'best-answer-card' : '' ?>" id="answer-<?= esc($answer['id_answer']) ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="user-info">
                                 <img src="<?= base_url('assets/images/profiles/' . esc($answer['user_photo'] ?? 'default.jpg')) ?>" alt="<?= esc($answer['user_nama']) ?>" width="25" height="25" class="rounded-circle me-1">
                                <?= esc($answer['user_nama']) ?>
                                <?php if ($answer['is_best_answer']): ?>
                                    <span class="best-answer-badge">Jawaban Terbaik</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted"><?= CodeIgniter\I18n\Time::parse($answer['created_at'])->humanize() ?></small>
                        </div>

                        <div class="answer-content mt-2">
                            <?= nl2br(esc($answer['content'])) ?>
                        </div>

                        <?php if (session()->get('isLoggedIn')): ?>
                            <?php
                                $isAnswerOwner = (session()->get('user_id') == $answer['id_user']);
                                $isAdmin = (session()->get('role') == 'admin');
                            ?>
                            <div class="mt-2 pt-2 border-top d-flex justify-content-end">
                                <?php if ($isAnswerOwner): // Tombol Edit hanya untuk pemilik jawaban ?>
                                    <a href="<?= site_url('answer/edit/' . $answer['id_answer']) ?>" class="btn btn-sm btn-outline-secondary me-2">Edit Jawaban</a>
                                <?php endif; ?>

                                <?php if ($isAnswerOwner || $isAdmin): // Tombol Hapus untuk pemilik ATAU admin ?>
                                    <form action="<?= site_url('answer/delete/' . $answer['id_answer']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jawaban ini?<?= $isAdmin && !$isAnswerOwner ? " (Sebagai Admin)" : "" ?>');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus Jawaban</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div> <span class="rating-summary-text">
                                    Rating:
                                    <strong id="avg-rating-<?= esc($answer['id_answer']) ?>">
                                        <?= number_format($answer['rating_stats']['average'], 1) ?>
                                    </strong> bintang
                                    (<span id="count-rating-<?= esc($answer['id_answer']) ?>"><?= esc($answer['rating_stats']['count']) ?></span> suara)
                                </span>

                                <?php if (session()->get('isLoggedIn') && session()->get('user_id') != $answer['id_user']): ?>
                                    <div class="star-rating mt-1" data-answer-id="<?= esc($answer['id_answer']) ?>">
                                        <small>Beri rating:</small>
                                        <?php $userGivenRating = $answer['user_given_rating']; ?>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?= ($i <= $userGivenRating) ? 'rated' : '' ?>" data-value="<?= $i ?>">&#9733;</span>
                                        <?php endfor; ?>
                                    </div>
                                    <div id="rating-feedback-message-<?= esc($answer['id_answer']) ?>" class="rating-feedback-message"></div>
                                <?php elseif(session()->get('isLoggedIn') && session()->get('user_id') == $answer['id_user']): ?>
                                    <div class="mt-1">
                                         <small class="text-muted rating-summary-text">Anda tidak bisa memberi rating pada jawaban sendiri.</small>
                                    </div>
                                <?php elseif(!session()->get('isLoggedIn')): ?>
                                     <div class="mt-1">
                                         <small class="rating-summary-text"><a href="<?= site_url('login?redirect=' . urlencode(current_url())) ?>">Masuk</a> untuk memberi rating.</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (session()->get('isLoggedIn') && $question['id_user'] == session()->get('user_id')): ?>
                                <div class="ms-auto"> <form action="<?= site_url('answer/toggle-best/' . $answer['id_answer']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <?php if ($answer['is_best_answer']): ?>
                                            <button type="submit" class="btn btn-sm btn-outline-warning toggle-best-answer-btn">
                                                Batalkan Jawaban Terbaik
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success toggle-best-answer-btn">
                                                Tandai Jawaban Terbaik
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            <?php endif; ?>
                            </div>
                    </div> <?php endforeach; ?>
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
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfTokenName = '<?= csrf_token() ?>';
        let csrfTokenValue = '<?= csrf_hash() ?>'; 

        document.querySelectorAll('.star-rating .star').forEach(star => {
            star.addEventListener('click', function () {
                const ratingValue = this.dataset.value;
                const parent = this.closest('.star-rating');
                const answerId = parent.dataset.answerId;
                const feedbackDiv = document.getElementById(`rating-feedback-message-${answerId}`);

                parent.querySelectorAll('.star').forEach(s => {
                    s.classList.remove('rated');
                    if (parseInt(s.dataset.value) <= ratingValue) {
                        s.classList.add('rated');
                    }
                });
                feedbackDiv.textContent = 'Menyimpan rating...';
                feedbackDiv.className = 'rating-feedback-message text-info';

                const formData = new URLSearchParams();
                formData.append('rating', ratingValue);
                formData.append(csrfTokenName, csrfTokenValue);

                fetch(`<?= site_url('answer/rate/') ?>${answerId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData.toString()
                })
                .then(response => {
                    const newCsrfToken = response.headers.get('X-CSRF-TOKEN');
                    if (newCsrfToken) {
                        csrfTokenValue = newCsrfToken;
                         // Jika kamu punya input CSRF global di halaman (misal: <meta name="csrf-token" content="...">)
                         // atau input hidden di form utama, update juga di sana.
                         // Contoh: document.querySelector('meta[name="csrf-token"]').setAttribute('content', newCsrfToken);
                         //         document.querySelector('input[name="' + csrfTokenName + '"]').value = newCsrfToken;
                    }
                    if (!response.ok) {
                        return response.json().then(errData => Promise.reject(errData));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        feedbackDiv.textContent = data.message;
                        feedbackDiv.className = 'rating-feedback-message text-success';
                        document.getElementById(`avg-rating-${answerId}`).textContent = data.average_rating; // Hanya nilai rata-rata
                        document.getElementById(`count-rating-${answerId}`).textContent = data.rating_count;
                    } else {
                        feedbackDiv.textContent = data.message || 'Gagal memberi rating.';
                        feedbackDiv.className = 'rating-feedback-message text-danger';
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    let errorMessage = 'Terjadi kesalahan.';
                    if (error && error.message) {
                        errorMessage = error.message;
                    } else if (typeof error === 'string') {
                        errorMessage = error;
                    }
                    feedbackDiv.textContent = errorMessage;
                    feedbackDiv.className = 'rating-feedback-message text-danger';
                });
            });
        });
    });
    </script>
</body>
</html>