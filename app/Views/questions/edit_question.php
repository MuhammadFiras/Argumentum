<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <title><?= esc($title ?? 'Edit Pertanyaan') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; color: #B92B27 !important; }
        .form-container { max-width: 700px; margin: 50px auto; background-color: white; padding:30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-submit { background-color: #B92B27; border-color: #B92B27; color:white; }
        .btn-submit:hover { background-color: #a32622; border-color: #a32622; }
        .invalid-feedback { display: block; }
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
                        <a href="<?= site_url('auth/register') ?>" class="btn btn-primary">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Edit Pertanyaan</h2>
            <hr>

            <?php if (!empty($question)): ?>
                <form action="<?= site_url('questions/update/' . $question['id_question']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="POST"> <div class="mb-3">
                        <label for="title" class="form-label">Judul Pertanyaan</label>
                        <input type="text" class="form-control <?= $validation->hasError('title') ? 'is-invalid' : '' ?>"
                               id="title" name="title"
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
                        <textarea class="form-control <?= $validation->hasError('content') ? 'is-invalid' : '' ?>"
                                  id="content" name="content" rows="5"
                                  placeholder="Tambahkan detail atau konteks untuk pertanyaanmu..."><?= old('content', esc($question['content'])) ?></textarea>
                        <?php if ($validation->hasError('content')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('content') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
                    <a href="<?= site_url('question/' . $question['slug']) ?>" class="btn btn-link">Batal</a>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">Pertanyaan tidak ditemukan atau Anda tidak memiliki akses.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        © Argumentum, <?= date('Y') ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>