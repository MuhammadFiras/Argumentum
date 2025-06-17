<?= $this->extend('layout/auth_layout') ?>

<?= $this->section('content') ?>
<div class="login-container position-relative" data-aos="fade-up">
    <a class="btn back-btn" href="<?= site_url('/'); ?>"><i class="bi bi-arrow-left"></i> Kembali</a>
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
            <input 
                type="email" 
                class="form-control <?= (isset(session()->getFlashdata('errors')['email'])) ? 'is-invalid' : ''; ?>" 
                id="email" 
                name="email" 
                value="<?= old('email') ?>" 
                required
            >
            <div class="invalid-feedback">
                <?= (isset(session()->getFlashdata('errors')['email'])) ? session()->getFlashdata('errors')['email'] : ''; ?>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Kata Sandi</label>
            <input 
                type="password" 
                class="form-control <?= (isset(session()->getFlashdata('errors')['password'])) ? 'is-invalid' : ''; ?>" 
                id="password" 
                name="password" 
                required
            >
            <div class="invalid-feedback">
                <?= (isset(session()->getFlashdata('errors')['password'])) ? session()->getFlashdata('errors')['password'] : ''; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100">Masuk</button>
    </form>

    <p class="mt-4 text-center">
        Belum punya akun? <a href="<?= site_url('/register') ?>" class="btn-link-custom fw-bold">Daftar</a>
    </p>

    <p class="footer-text">
        Tentang Kami · Karier · Privasi · Ketentuan · Kontak · Bahasa - Pers · © Argumentum, <?= date('Y') ?>
    </p>
</div>
<?= $this->endSection() ?>