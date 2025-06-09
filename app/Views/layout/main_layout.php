<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>

    <title><?= esc($title ?? 'Argumentum') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100 <?= $bodyClass ?? '' ?>">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top ">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">Argumentum</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-bold">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Beranda</a></li>
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
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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

    <div class="main-content-wrapper">
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="bg-danger text-white mt-4 pt-3 pb-4">
        <div class="container">
            <hr class="pb-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                <div class="col mb-4 text-center text-md-start">
                    <h5 class="fw-bold">Argumentum</h5>
                    <p class="text-white">© 2025 Argumentum</p>
                </div>

                <div class="col mb-4">
                    <h5 class="fw-bold">Muhammad Firas</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2 text-white">2210817110014</li>
                    </ul>
                </div>

                <div class="col mb-4">
                    <h5 class="fw-bold">Muhammad Raihan</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2 text-white">2310817110008</li>
                    </ul>
                </div>

                <div class="col mb-4">
                    <h5 class="fw-bold">Adrian Bintang Saputera</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2 text-white">2310817110006</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 700,
                easing: 'ease-in-out',
                once: true,
            });
        });
    </script>


    <?= $this->renderSection('scripts') ?>
</body>

</html>