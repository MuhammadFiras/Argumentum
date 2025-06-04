<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Argumentum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('<?= base_url('public/foto_perpus.jpg') ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: sans-serif;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #B92B27; /* Warna merah khas Quora */
            font-weight: bold;
        }
        .login-container p.tagline {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
        }
        .form-control:focus {
            border-color: #B92B27;
            box-shadow: 0 0 0 0.25rem rgba(185, 43, 39, 0.25);
        }
        .btn-login {
            background-color: #B92B27;
            border-color: #B92B27;
            color: white;
            font-weight: bold;
        }
        .btn-login:hover {
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
        /* CSS untuk .social-login .btn dan .or-separator dapat dihapus jika tidak ada elemen lain yang menggunakannya */
        /* Namun, untuk saat ini kita biarkan jika ada kemungkinan penggunaan lain atau untuk kesederhanaan */
        /* .social-login .btn {
            width: 100%;
            margin-bottom: 10px;
        } */
        /* .or-separator {
            display: flex;
            align-items: center;
            text-align: center;
            color: #aaa;
            margin: 20px 0;
        }
        .or-separator::before,
        .or-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        .or-separator:not(:empty)::before {
            margin-right: .25em;
        }
        .or-separator:not(:empty)::after {
            margin-left: .25em;
        } */
        .footer-text {
            font-size: 0.85rem;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Argumentum</h2>
        <p class="tagline">Tempat berbagi pengetahuan dan memahami dunia lebih baik</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="<?= site_url('auth/processLogin') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                    <label class="form-check-label" for="remember_me">Ingat saya</label>
                </div>
                <a href="#" class="btn-link-custom">Lupa kata sandi?</a>
            </div>
            <button type="submit" class="btn btn-login w-100">Masuk</button>
        </form>

        <p class="mt-4 text-center">
            Belum punya akun? <a href="<?= site_url('auth/register') ?>" class="btn-link-custom fw-bold">Daftar</a>
        </p>

        <p class="footer-text">
            Tentang Kami · Karier · Privasi · Ketentuan · Kontak · Bahasa - Pers · © Argumentum, <?= date('Y') ?>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            let isValid = true;
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');

            // Reset error messages
            emailError.style.display = 'none';
            emailError.textContent = '';
            passwordError.style.display = 'none';
            passwordError.textContent = '';
            email.classList.remove('is-invalid');
            password.classList.remove('is-invalid');

            // Validasi Email
            if (email.value.trim() === '') {
                emailError.textContent = 'Email tidak boleh kosong.';
                emailError.style.display = 'block';
                email.classList.add('is-invalid');
                isValid = false;
            } else {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value)) {
                    emailError.textContent = 'Format email tidak valid.';
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
            }

            if (!isValid) {
                event.preventDefault(); // Mencegah form submit jika tidak valid
            }
        });
    </script>
</body>
</html>