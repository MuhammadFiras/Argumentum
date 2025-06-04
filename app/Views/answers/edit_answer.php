<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <title><?= esc($title ?? 'Edit Jawaban') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; color: #B92B27 !important; }
        .form-container { max-width: 700px; margin: 50px auto; background-color: white; padding:30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-submit { background-color: #B92B27; border-color: #B92B27; color:white; }
        .btn-submit:hover { background-color: #a32622; border-color: #a32622; }
        .invalid-feedback { display: block; }
        .original-question-preview {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 0.9em;
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
                    <?php if (session()->get('isLoggedIn') && $question): ?>
                    <li class="nav-item">
                         <a class="nav-link" href="<?= site_url('question/' . $question['slug']) ?>">Kembali ke Pertanyaan</a>
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
                    <input type="hidden" name="_method" value="POST"> <div class="mb-3">
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

    <footer class="text-center mt-5 mb-3 text-muted">
        © Argumentum, <?= date('Y') ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>