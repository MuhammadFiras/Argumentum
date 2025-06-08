<?= $this->extend('layout/main_layout') ?>

<?= $this->section('title') ?>
    <?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
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
            <form action="<?= site_url('/profile/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">

                <div class="text-center mb-3">
                    <?php
                        $photo_url = base_url('default.jpg');
                        if (!empty($user_data['photo_profile']) && $user_data['photo_profile'] != 'default.jpg') {
                            $photo_url = base_url('assets/images/profiles/' . esc($user_data['photo_profile']));
                        }
                    ?>
                    <img src="<?= $photo_url ?>" alt="Foto Profil <?= esc($user_data['nama_lengkap']) ?>" class="profile-picture">
                    
                    <div class="mt-3">
                        <label for="photo_profile" class="btn btn-outline-secondary">Ganti Foto Profil</label>
                        <input type="file" id="photo_profile" name="photo_profile" class="d-none">
                        <small class="d-block text-muted mt-1">Maks 1MB (jpg, jpeg, png)</small>
                    </div>
                     <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['photo_profile'])): ?>
                        <div class="text-danger mt-1" style="font-size: 0.875em;"><?= isset(session()->getFlashdata('errors')['photo_profile']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" <?= $validation->hasError('nama_lengkap') ? 'is-invalid' : '' ?>
                           id="nama_lengkap" name="nama_lengkap"
                           value="<?= old('nama_lengkap', esc($user_data['nama_lengkap'])) ?>" required>
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_lengkap'])): ?>
                        <div class="invalid-feedback"><?= session()->getFlashdata('errors')['nama_lengkap'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email (Tidak dapat diubah)</label>
                    <input type="email" class="form-control" id="email" value="<?= esc($user_data['email']) ?>" readonly disabled>
                </div>
                
                <div class="mb-3">
                    <label for="credentials" class="form-label">Kredensial Singkat</label>
                    <input type="text" class="form-control <?= $validation->hasError('credentials') ? 'is-invalid' : '' ?>"
                           id="credentials" name="credentials"
                           value="<?= old('credentials', esc($user_data['credentials'] ?? '')) ?>"
                           placeholder="Contoh: Mahasiswa di Univ. X, Web Developer">
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['credentials'])): ?>
                        <div class="invalid-feedback"><?= session()->getFlashdata('errors')['credentials'] ?></div>
                    <?php endif; ?>
                </div>
                
                <?php //lanjutan getError dibawah ?>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi Profil (Bio)</label>
                    <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>"
                              id="description" name="description" rows="4"
                              placeholder="Ceritakan sedikit tentang diri Anda..."><?= old('description', esc($user_data['description'] ?? '')) ?></textarea>
                    <?php if ($validation->hasError('description')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('description') ?></div>
                    <?php endif; ?>
                </div>

                <h5 class="mt-4">Link Sosial Media</h5>
                <div class="mb-3">
                    <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                    <input type="url" class="form-control <?= $validation->hasError('linkedin_url') ? 'is-invalid' : '' ?>"
                           id="linkedin_url" name="linkedin_url"
                           value="<?= old('linkedin_url', esc($user_data['linkedin_url'] ?? '')) ?>"
                           placeholder="https://www.linkedin.com/in/usernameanda">
                    <?php if ($validation->hasError('linkedin_url')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('linkedin_url') ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="instagram_url" class="form-label">Instagram URL</label>
                    <input type="url" class="form-control <?= $validation->hasError('instagram_url') ? 'is-invalid' : '' ?>"
                           id="instagram_url" name="instagram_url"
                           value="<?= old('instagram_url', esc($user_data['instagram_url'] ?? '')) ?>"
                           placeholder="https://www.instagram.com/usernameanda">
                    <?php if ($validation->hasError('instagram_url')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('instagram_url') ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-save">Simpan Perubahan</button>
                <a href="<?= site_url('/profile') ?>" class="btn btn-link">Batal</a>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">Data pengguna tidak ditemukan.</div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>