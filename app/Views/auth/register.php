<?= $this->extend('layout/auth_layout') ?>

<?= $this->section('content') ?>
<div class="register-container position-relative" data-aos="fade-up">
    <a class="btn back-btn" href="<?= site_url('/'); ?>"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h2>Argumentum</h2>
    <p class="tagline">Bergabunglah dan mulai berbagi pengetahuan</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form id="registerForm" action="<?= site_url('auth/processRegister') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input
                type="text"
                class="form-control <?= (isset(session()->getFlashdata('errors')['nama_lengkap'])) ? 'is-invalid' : ''; ?>"
                id="nama_lengkap"
                name="nama_lengkap"
                value="<?= old('nama_lengkap') ?>"
                required>
            <div class="invalid-feedback">
                <?= (isset(session()->getFlashdata('errors')['nama_lengkap'])) ? session()->getFlashdata('errors')['nama_lengkap'] : ''; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                class="form-control <?= (isset(session()->getFlashdata('errors')['email'])) ? 'is-invalid' : ''; ?>"
                id="email"
                name="email"
                value="<?= old('email') ?>"
                required>
            <div class="invalid-feedback">
                <?= (isset(session()->getFlashdata('errors')['email'])) ? session()->getFlashdata('errors')['email'] : ''; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input
                type="password"
                class="form-control <?= (isset(session()->getFlashdata('errors')['password'])) ? 'is-invalid' : ''; ?>"
                id="password"
                name="password"
                required>
            <div class="invalid-feedback">
                <?= (isset(session()->getFlashdata('errors')['password'])) ? session()->getFlashdata('errors')['password'] : ''; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi</label>
            <input
                type="password"
                class="form-control <?= (isset(session()->getFlashdata('errors')['confirm_password'])) ? 'is-invalid' : ''; ?>"
                id="confirm_password"
                name="confirm_password"
                required>
            <div class="invalid-feedback">
                <?= (isset(session()->getFlashdata('errors')['confirm_password'])) ? session()->getFlashdata('errors')['confirm_password'] : ''; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="photo_profile" class="form-label">Tambah Foto Profil (opsional)</label>
            <input class="form-control <?= (isset(session()->getFlashdata('errors')['photo_profile'])) ? 'is-invalid' : ''; ?>" type="file" id="photo_profile" name="photo_profile">
            <div class="invalid-feedback">
                <?= (isset(session()->getFlashdata('errors')['photo_profile'])) ? session()->getFlashdata('errors')['photo_profile'] : ''; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-register w-100">Daftar</button>
    </form>

    <p class="mt-4 text-center">
        Sudah punya akun? <a href="<?= site_url('/login') ?>" class="btn-link-custom fw-bold">Masuk</a>
    </p>

    <p class="footer-text">
        Tentang Kami · Karier · Privasi · Ketentuan · Kontak · Bahasa - Pers · © Argumentum, <?= date('Y') ?>
    </p>
</div>
<?= $this->endSection() ?>