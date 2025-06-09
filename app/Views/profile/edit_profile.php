<?= $this->extend('layout/main_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="form-container">
        <?php if (!empty($user_data)): ?>
            <div class="profile-header text-center mb-4">
                <?php
                $photo_url = base_url('default.jpg');
                if (!empty($user_data['photo_profile']) && $user_data['photo_profile'] != 'default.jpg') {
                    $photo_url = base_url('assets/images/profiles/' . esc($user_data['photo_profile']));
                }
                ?>
                <img src="<?= $photo_url ?>" alt="Foto Profil <?= esc($user_data['nama_lengkap']) ?>" class="profile-picture mx-auto">

                <h2 class="profile-name mt-3"><?= esc($user_data['nama_lengkap']) ?></h2>
                <p class="profile-credentials"><?= esc($user_data['credentials'] ?? '') ?></p>
            </div>

            <form action="<?= site_url('/profile/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">

                <div class="mb-3 text-center">
                    <label for="photo_profile" class="btn btn-outline-secondary">Ganti Foto Profil</label>
                    <input type="file" id="photo_profile" name="photo_profile" class="d-none">
                    <small class="d-block text-muted mt-1">Maks 1MB (jpg, jpeg, png)</small>
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['photo_profile'])): ?>
                        <div class="text-danger mt-1" style="font-size: 0.875em;"><?= session()->getFlashdata('errors')['photo_profile'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['nama_lengkap']) ? 'is-invalid' : '' ?>"
                        id="nama_lengkap" name="nama_lengkap"
                        value="<?= old('nama_lengkap', esc($user_data['nama_lengkap'])) ?>" required>
                    <div class="invalid-feedback"><?= isset(session()->getFlashdata('errors')['nama_lengkap']) ? session()->getFlashdata('errors')['nama_lengkap'] : '' ?></div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email (Tidak dapat diubah)</label>
                    <input type="email" class="form-control" id="email" value="<?= esc($user_data['email']) ?>" readonly disabled>
                </div>

                <div class="mb-3">
                    <label for="credentials" class="form-label">Kredensial Singkat</label>
                    <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['credentials']) ? 'is-invalid' : '' ?>"
                        id="credentials" name="credentials"
                        value="<?= old('credentials', esc($user_data['credentials'] ?? '')) ?>"
                        placeholder="Contoh: Mahasiswa di Univ. X, Web Developer">
                    <div class="invalid-feedback"><?= isset(session()->getFlashdata('errors')['credentials']) ? session()->getFlashdata('errors')['credentials'] : '' ?></div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi Profil (Bio)</label>
                    <textarea class="form-control <?= isset(session()->getFlashdata('errors')['description']) ? 'is-invalid' : '' ?>"
                        id="description" name="description" rows="4"
                        placeholder="Ceritakan sedikit tentang diri Anda..."><?= old('description', esc($user_data['description'] ?? '')) ?></textarea>
                    <div class="invalid-feedback"><?= isset(session()->getFlashdata('errors')['description']) ? session()->getFlashdata('errors')['description'] : '' ?></div>
                </div>

                <h5 class="mt-4">Link Sosial Media</h5>
                <div class="mb-3">
                    <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                    <input type="url" class="form-control <?= isset(session()->getFlashdata('errors')['linkedin_url']) ? 'is-invalid' : '' ?>"
                        id="linkedin_url" name="linkedin_url"
                        value="<?= old('linkedin_url', esc($user_data['linkedin_url'] ?? '')) ?>"
                        placeholder="https://www.linkedin.com/in/usernameanda">
                    <div class="invalid-feedback"><?= isset(session()->getFlashdata('errors')['linkedin_url']) ? session()->getFlashdata('errors')['linkedin_url'] : '' ?></div>
                </div>

                <div class="mb-3">
                    <label for="instagram_url" class="form-label">Instagram URL</label>
                    <input type="url" class="form-control <?= isset(session()->getFlashdata('errors')['instagram_url']) ? 'is-invalid' : '' ?>"
                        id="instagram_url" name="instagram_url"
                        value="<?= old('instagram_url', esc($user_data['instagram_url'] ?? '')) ?>"
                        placeholder="https://www.instagram.com/usernameanda">
                    <div class="invalid-feedback"><?= isset(session()->getFlashdata('errors')['instagram_url']) ? session()->getFlashdata('errors')['instagram_url'] : '' ?></div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="<?= site_url('/profile') ?>" class="btn btn-link">Batal</a>
                    <button type="submit" class="btn btn-save">Simpan Perubahan</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">Data pengguna tidak ditemukan.</div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
