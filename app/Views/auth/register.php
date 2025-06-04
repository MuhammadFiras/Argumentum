<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Argumentum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Ganti 'placeholder-background.jpg' dengan nama file gambar Anda */
            background-image: url('<?= base_url('public/foto_perpus.jpg') ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: sans-serif;
            padding: 20px 0; /* Tambahkan padding untuk scroll jika konten panjang */
        }
        .register-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px 40px; /* Sesuaikan padding */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 550px; /* Lebarkan sedikit untuk form registrasi */
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #B92B27; /* Warna merah khas Quora */
            font-weight: bold;
        }
        .register-container p.tagline {
            text-align: center;
            margin-bottom: 25px;
            color: #666;
        }
        .form-control:focus {
            border-color: #B92B27;
            box-shadow: 0 0 0 0.25rem rgba(185, 43, 39, 0.25);
        }
        .btn-register {
            background-color: #B92B27;
            border-color: #B92B27;
            color: white;
            font-weight: bold;
        }
        .btn-register:hover {
            background-color: #a32622;
            border-color: #a32622;
        }
        .btn-link-custom {
            color: #B92B27;
            text-decoration: none;
        }
        .btn-link-custom:hover {
            text-decoration: underline;
        }
        .footer-text {
            font-size: 0.85rem;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
        /* Untuk pesan error */
        .invalid-feedback {
            display: block; /* Pastikan pesan error selalu terlihat jika ada */
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Argumentum</h2>
        <p class="tagline">Bergabunglah dan mulai berbagi pengetahuan</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" action="<?= site_url('auth/processRegister') ?>" method="post">            <?= csrf_field() ?> <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_lengkap'])): ?>
                    <div class="invalid-feedback">
                        <?= session()->getFlashdata('errors')['nama_lengkap'] ?>
                    </div>
                <?php endif; ?>
                <div id="namaLengkapError" class="invalid-feedback" style="display:none;"></div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Surel</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                    <div class="invalid-feedback">
                        <?= session()->getFlashdata('errors')['email'] ?>
                    </div>
                <?php endif; ?>
                <div id="emailError" class="invalid-feedback" style="display:none;"></div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password'])): ?>
                    <div class="invalid-feedback">
                        <?= session()->getFlashdata('errors')['password'] ?>
                    </div>
                <?php endif; ?>
                <div id="passwordError" class="invalid-feedback" style="display:none;"></div>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['confirm_password'])): ?>
                    <div class="invalid-feedback">
                        <?= session()->getFlashdata('errors')['confirm_password'] ?>
                    </div>
                <?php endif; ?>
                <div id="confirmPasswordError" class="invalid-feedback" style="display:none;"></div>
            </div>

            <button type="submit" class="btn btn-register w-100">Daftar</button>
        </form>

        <p class="mt-4 text-center">
            Sudah punya akun? <a href="<?= site_url('auth/login') ?>" class="btn-link-custom fw-bold">Masuk</a>
        </p>

        <p class="footer-text">
            Tentang Kami · Karier · Privasi · Ketentuan · Kontak · Bahasa - Pers · © Argumentum, <?= date('Y') ?>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            let isValid = true;
            const namaLengkap = document.getElementById('nama_lengkap');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const agreeTerms = document.getElementById('agreeTerms');

            const namaLengkapError = document.getElementById('namaLengkapError');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');

            // Reset error messages
            [namaLengkap, email, password, confirmPassword].forEach(input => {
                input.classList.remove('is-invalid');
                document.getElementById(input.id + 'Error').style.display = 'none';
                document.getElementById(input.id + 'Error').textContent = '';
            });
            agreeTerms.classList.remove('is-invalid');
            agreeTermsError.style.display = 'none';
            agreeTermsError.textContent = '';


            // Validasi Nama Lengkap
            if (namaLengkap.value.trim() === '') {
                namaLengkapError.textContent = 'Nama lengkap tidak boleh kosong.';
                namaLengkapError.style.display = 'block';
                namaLengkap.classList.add('is-invalid');
                isValid = false;
            }

            // Validasi Email
            if (email.value.trim() === '') {
                emailError.textContent = 'Surel tidak boleh kosong.';
                emailError.style.display = 'block';
                email.classList.add('is-invalid');
                isValid = false;
            } else {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value)) {
                    emailError.textContent = 'Format surel tidak valid.';
                    emailError.style.display = 'block';
                    email.classList.add('is-invalid');
                    isValid = false;
                }
            }

            // Validasi Password
            if (password.value.trim() === '') {
                passwordError.textContent = 'Kata sandi tidak boleh kosong.';
                passwordError.style.display = 'block';
                password.classList.add('is-invalid');
                isValid = false;
            } else if (password.value.length < 8) { // Contoh: minimal 8 karakter
                passwordError.textContent = 'Kata sandi minimal 8 karakter.';
                passwordError.style.display = 'block';
                password.classList.add('is-invalid');
                isValid = false;
            }

            // Validasi Konfirmasi Password
            if (confirmPassword.value.trim() === '') {
                confirmPasswordError.textContent = 'Konfirmasi kata sandi tidak boleh kosong.';
                confirmPasswordError.style.display = 'block';
                confirmPassword.classList.add('is-invalid');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                confirmPasswordError.textContent = 'Konfirmasi kata sandi tidak cocok.';
                confirmPasswordError.style.display = 'block';
                confirmPassword.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault(); // Mencegah form submit jika tidak valid
            }
        });
    </script>
</body>
</html>