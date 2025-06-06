<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <title><?= esc($title ?? 'Edit Profil') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: sans-serif;
        }

        .form-container {
            max-width: 700px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-save {
            background-color: #B92B27;
            border-color: #B92B27;
            color: white;
        }

        .btn-save:hover {
            background-color: #a32622;
            border-color: #a32622;
        }

        .invalid-feedback {
            display: block;
        }

        .navbar-brand {
            font-weight: bold;
            color: #B92B27 !important;
        }

        .profile-picture {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
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
                                <li><a class="dropdown-item active" href="<?= site_url('/profile/edit') ?>">Edit Profil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if (!empty($user_data)): ?>
                <form action="<?= site_url('/profile/update') ?>" method="post" enctype="multipart/form-data"> <?= csrf_field() ?>
                    <div class="text-center">
                        <?php
                        $photo_url = base_url('default.jpg');
                        if (!empty($user_data['photo_profile']) && $user_data['photo_profile'] != 'default.jpg') {
                            $specific_photo_path = 'assets/images/profiles/' . esc($user_data['photo_profile']);
                            $photo_url = base_url($specific_photo_path);
                        }
                        ?>
                        <div class="row mb-3">
                            <div class="col">
                                <img src="<?= $photo_url ?>" alt="Foto Profil <?= esc($user_data['nama_lengkap']) ?>" class="profile-picture">
                            </div>
                        </div>
                        <?php if (isset(session()->getFlashdata('errors')['photo_profile'])): ?>
                            <div class="row">
                                <div class="col">
                                    <p class="text-danger"><?= session()->getFlashdata('errors')['photo_profile'] ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col">
                                <!-- Input file yang disembunyikan -->
                                <input type="file" id="photo_profile" name="photo_profile" style="display: none;">

                                <!-- Tombol yang terlihat -->
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('photo_profile').click()">Edit Foto Profil</button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="_method" value="POST">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= (isset(session()->getFlashdata('errors')['nama_lengkap'])) ? 'is-invalid' : ''; ?>"
                            id="nama_lengkap" name="nama_lengkap"
                            value="<?= old('nama_lengkap', esc($user_data['nama_lengkap'])) ?>" required>
                        <div class="invalid-feedback">
                            <?= (isset(session()->getFlashdata('errors')['nama_lengkap'])) ? session()->getFlashdata('errors')['nama_lengkap'] : ''; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email (Tidak dapat diubah)</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= esc($user_data['email']) ?>" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label for="credentials" class="form-label">Kredensial Singkat</label>
                        <input type="text" class="form-control <?= (isset(session()->getFlashdata('errors')['credentials'])) ? 'is-invalid' : ''; ?>"
                            id="credentials" name="credentials"
                            value="<?= old('credentials', esc($user_data['credentials'] ?? '')) ?>"
                            placeholder="Contoh: Mahasiswa di Univ. X, Web Developer">
                        <div class="invalid-feedback">
                            <?= (isset(session()->getFlashdata('errors')['credentials'])) ? session()->getFlashdata('errors')['credentials'] : ''; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Profil (Bio)</label>
                        <textarea class="form-control <?= (isset(session()->getFlashdata('errors')['description'])) ? 'is-invalid' : ''; ?>"
                            id="description" name="description" rows="4"
                            placeholder="Ceritakan sedikit tentang diri Anda..."><?= old('description', esc($user_data['description'] ?? '')) ?></textarea>
                        <div class="invalid-feedback">
                            <?= (isset(session()->getFlashdata('errors')['description'])) ? session()->getFlashdata('errors')['description'] : ''; ?>
                        </div>
                    </div>

                    <h5 class="mt-4">Link Sosial Media</h5>
                    <div class="mb-3">
                        <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                        <input type="url" class="form-control <?= (isset(session()->getFlashdata('errors')['linkedin_url'])) ? 'is-invalid' : ''; ?>"
                            id="linkedin_url" name="linkedin_url"
                            value="<?= old('linkedin_url', esc($user_data['linkedin_url'] ?? '')) ?>"
                            placeholder="https://www.linkedin.com/in/usernameanda">
                        <div class="invalid-feedback">
                            <?= (isset(session()->getFlashdata('errors')['linkedin_url'])) ? session()->getFlashdata('errors')['linkedin_url'] : ''; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="instagram_url" class="form-label">Instagram URL</label>
                        <input type="url" class="form-control <?= (isset(session()->getFlashdata('errors')['instagram_url'])) ? 'is-invalid' : ''; ?>"
                            id="instagram_url" name="instagram_url"
                            value="<?= old('instagram_url', esc($user_data['instagram_url'] ?? '')) ?>"
                            placeholder="https://www.instagram.com/usernameanda">
                        <div class="invalid-feedback">
                            <?= (isset(session()->getFlashdata('errors')['instagram_url'])) ? session()->getFlashdata('errors')['instagram_url'] : ''; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-save">Simpan Perubahan</button>
                    <a href="<?= site_url('/profile') ?>" class="btn btn-link">Batal</a>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">Data pengguna tidak ditemukan.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        © Argumentum, <?= date('Y') ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('photo_profile').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-picture').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>