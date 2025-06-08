<?= $this->extend('layout/main_layout') ?>

<?= $this->section('title') ?>
    <?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4 mb-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (!empty($user_profile)): ?>
        <div class="profile-header text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <?php
                        $photo_url = base_url('default.jpg');
                        if (!empty($user_profile['photo_profile']) && $user_profile['photo_profile'] != 'default.jpg') {
                            $photo_url = base_url('assets/images/profiles/' . esc($user_profile['photo_profile']));
                        }
                    ?>
                    <img src="<?= $photo_url ?>" alt="Foto Profil <?= esc($user_profile['nama_lengkap']) ?>" class="profile-picture">
                </div>
                <div class="col-md-9">
                    <h1 class="profile-name"><?= esc($user_profile['nama_lengkap']) ?></h1>
                    <?php if (!empty($user_profile['credentials'])): ?>
                        <p class="profile-credentials"><?= esc($user_profile['credentials']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user_profile['description'])): ?>
                        <p class="profile-description"><?= nl2br(esc($user_profile['description'])) ?></p>
                    <?php endif; ?>
                    <div class="social-links mt-3">
                        <?php if (!empty($user_profile['linkedin_url'])): ?>
                            <a href="<?= esc($user_profile['linkedin_url'], 'attr') ?>" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($user_profile['instagram_url'])): ?>
                            <a href="<?= esc($user_profile['instagram_url'], 'attr') ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php if ($is_own_profile): ?>
                        <a href="<?= site_url('/profile/edit') ?>" class="btn btn-outline-primary btn-sm mt-3">Edit Profil</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <ul class="nav nav-tabs mb-3" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions-content" type="button" role="tab" aria-controls="questions-content" aria-selected="true">Pertanyaan (<?= count($questions_by_user) ?>)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="answers-tab" data-bs-toggle="tab" data-bs-target="#answers-content" type="button" role="tab" aria-controls="answers-content" aria-selected="false">Jawaban (<?= count($answers_by_user) ?>)</button>
                    </li>
                </ul>
                <div class="tab-content" id="profileTabContent">
                    <div class="tab-pane fade show active content-section" id="questions-content" role="tabpanel" aria-labelledby="questions-tab">
                        <h5>Pertanyaan Dibuat</h5>
                        <?php if (!empty($questions_by_user)): ?>
                            <ul class="list-unstyled">
                                <?php foreach ($questions_by_user as $question): ?>
                                    <li class="list-item">
                                        <div class="list-item-title">
                                            <a href="<?= site_url('question/' . esc($question['slug'])) ?>"><?= esc($question['title']) ?></a>
                                        </div>
                                        <small class="text-muted">Dibuat: <?= CodeIgniter\I18n\Time::parse($question['created_at'])->toLocalizedString('d MMMM yyyy') ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted"><?= ($is_own_profile) ? 'Anda belum membuat pertanyaan.' : 'Pengguna ini belum membuat pertanyaan.' ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="tab-pane fade content-section" id="answers-content" role="tabpanel" aria-labelledby="answers-tab">
                        <h5>Jawaban Diberikan</h5>
                        <?php if (!empty($answers_by_user)): ?>
                            <ul class="list-unstyled">
                                <?php foreach ($answers_by_user as $answer): ?>
                                    <li class="list-item">
                                        <div class="list-item-title">
                                            <a href="<?= site_url('question/' . esc($answer['question_slug']) . '#answer-' . $answer['id_answer']) ?>">
                                                Jawaban untuk: <?= esc($answer['question_title']) ?>
                                            </a>
                                        </div>
                                        <p class="list-item-content"><?= word_limiter(esc($answer['content']), 30) ?></p>
                                        <small class="text-muted">Diberikan: <?= CodeIgniter\I18n\Time::parse($answer['created_at'])->toLocalizedString('d MMMM yyyy') ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted"><?= ($is_own_profile) ? 'Anda belum memberikan jawaban.' : 'Pengguna ini belum memberikan jawaban.' ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <p class="alert alert-warning">Profil pengguna tidak dapat ditemukan.</p>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>