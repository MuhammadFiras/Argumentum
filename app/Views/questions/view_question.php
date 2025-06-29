<?= $this->extend('layout/main_layout') ?>

<?= $this->section('content') ?>
<div class="container mt-4" data-aos="fade-up">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($question)): ?>
        <div class="question-header">
            <h1><?= esc($question['title']) ?></h1>
            <div class="user-info mb-2 d-flex align-items-center">
                <?php
                $question_user_photo_url = base_url('default.jpg');
                if (!empty($question['user_photo']) && $question['user_photo'] != 'default.jpg') {
                    $question_user_photo_url = base_url('assets/images/profiles/' . esc($question['user_photo']));
                }
                ?>
                <a href="<?= site_url('profile/' . esc($question['id_user'])) ?>" class="d-flex align-items-center text-decoration-none text-dark">
                    <img src="<?= $question_user_photo_url ?>" alt="<?= esc($question['user_nama']) ?>" width="30" height="30" class="rounded-circle me-2">
                    <span><?= esc($question['user_nama']) ?></span>
                </a>
                <span class="ms-2">· <?= CodeIgniter\I18n\Time::parse($question['created_at'])->humanize() ?></span>

                <?php if (!empty($question['topics'])): ?>
                    <div class="topics-container d-flex align-items-center ms-3">
                        <?php
                        $topic_array = explode(', ', $question['topics']);
                        ?>
                        <?php foreach ($topic_array as $topic_name): ?>
                            <span class="badge bg-secondary me-1"><?= esc($topic_name) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <hr>
            <div class="question-content">
                <?= nl2br(esc($question['content'])) ?>
            </div>

            <?php if (session()->get('isLoggedIn')): ?>
                <?php
                $isOwner = (session()->get('user_id') == $question['id_user']);
                $isAdmin = (session()->get('role') == 'admin');
                ?>
                <div class="mt-3">
                    <?php if ($isOwner): ?>
                        <a href="<?= site_url('questions/edit/' . $question['id_question']) ?>" class="btn btn-sm btn-outline-secondary">Edit Pertanyaan</a>
                    <?php endif; ?>

                    <?php if ($isOwner || $isAdmin): ?>
                        <form action="<?= site_url('questions/delete/' . $question['id_question']) ?>" method="post" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?<?= $isAdmin && !$isOwner ? " (Sebagai Admin)" : "" ?>');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus Pertanyaan</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <h4 class="mt-4 mb-3"><?= count($answers) ?> Jawaban</h4>

        <?php if (session()->get('isLoggedIn')): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tulis Jawaban Anda</h5>
                    <form action="<?= site_url('answer/submit/' . $question['id_question']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <textarea class="form-control <?= ($validation_answer->hasError('answer_content')) ? 'is-invalid' : '' ?>" name="answer_content" rows="4" placeholder="Tulis jawabanmu di sini..." required><?= old('answer_content') ?></textarea>
                            <?php if ($validation_answer->hasError('answer_content')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $validation_answer->getError('answer_content') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-submit-answer">Kirim Jawaban</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p><a href="<?= site_url('login?redirect=' . urlencode(current_url())) ?>">Masuk</a> untuk menjawab pertanyaan ini.</p>
        <?php endif; ?>

        <?php if (!empty($answers)): ?>
            <?php foreach ($answers as $answer): ?>
                <div class="answer-card <?= $answer['is_best_answer'] ? 'best-answer-card' : '' ?>" id="answer-<?= esc($answer['id_answer']) ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="user-info d-flex align-items-center">
                            <a href="<?= site_url('profile/' . esc($answer['id_user'])) ?>" class="text-decoration-none text-dark">
                                <?php
                                $answer_user_photo_url = base_url('default.jpg');
                                if (!empty($answer['user_photo']) && $answer['user_photo'] != 'default.jpg') {
                                    $answer_user_photo_url = base_url('assets/images/profiles/' . esc($answer['user_photo']));
                                }
                                ?>
                                <img src="<?= $answer_user_photo_url ?>" alt="<?= esc($answer['user_nama']) ?>" width="25" height="25" class="rounded-circle me-2">
                            </a>
                            <a href="<?= site_url('profile/' . esc($answer['id_user'])) ?>" class="text-decoration-none text-dark fw-bold">
                                <span><?= esc($answer['user_nama']) ?></span>
                            </a>
                            <?php if ($answer['is_best_answer']): ?>
                                <span class="best-answer-badge">Jawaban Terbaik</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><?= CodeIgniter\I18n\Time::parse($answer['created_at'])->humanize() ?></small>
                    </div>

                    <div class="answer-content mt-2">
                        <?= nl2br(esc($answer['content'])) ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                        <div>
                            <span class="rating-summary-text">
                                Rating:
                                <strong id="avg-rating-<?= esc($answer['id_answer']) ?>">
                                    <?= number_format($answer['rating_stats']['average'], 1) ?>
                                </strong>
                                (<span id="count-rating-<?= esc($answer['id_answer']) ?>"><?= esc($answer['rating_stats']['count']) ?></span> suara)
                            </span>

                            <?php if (session()->get('isLoggedIn') && session()->get('user_id') != $answer['id_user']): ?>
                                <div class="star-rating-container d-flex align-items-center">
                                    <div class="star-rating mt-1" data-answer-id="<?= esc($answer['id_answer']) ?>" data-current-rating="<?= esc($answer['user_given_rating']) ?>">
                                        <small>Beri rating:</small>
                                        <?php $userGivenRating = $answer['user_given_rating']; ?>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?= ($i <= $userGivenRating) ? 'rated' : '' ?>" data-value="<?= $i ?>">&#9733;</span>
                                        <?php endfor; ?>
                                    </div>

                                    <a href="#"
                                        class="btn-delete-rating small text-decoration-none text-danger ms-3"
                                        data-answer-id="<?= esc($answer['id_answer']) ?>"
                                        style="<?= ($answer['user_given_rating'] > 0) ? 'display: inline;' : 'display: none;' ?>">
                                        Hapus Rating
                                    </a>
                                </div>
                                <div id="rating-feedback-message-<?= esc($answer['id_answer']) ?>" class="rating-feedback-message"></div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <?php if (session()->get('isLoggedIn')): ?>
                                <?php
                                $isAnswerOwner = (session()->get('user_id') == $answer['id_user']);
                                $isAdmin = (session()->get('role') == 'admin');
                                $isQuestionOwner = (session()->get('user_id') == $question['id_user']);
                                ?>

                                <?php if ($isQuestionOwner): ?>
                                    <form action="<?= site_url('answer/toggle-best/' . $answer['id_answer']) ?>" method="post" class="d-inline ms-2">
                                        <?= csrf_field() ?>
                                        <?php if ($answer['is_best_answer']): ?>
                                            <button type="submit" class="btn btn-sm btn-outline-warning toggle-best-answer-btn">Batalkan Jawaban Terbaik</button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success toggle-best-answer-btn">Tandai Jawaban Terbaik</button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>

                                <?php if ($isAnswerOwner): ?>
                                    <a href="<?= site_url('answer/edit/' . $answer['id_answer']) ?>" class="btn btn-sm btn-outline-secondary me-2">Edit Jawaban</a>
                                <?php endif; ?>

                                <?php if ($isAnswerOwner || $isAdmin): ?>
                                    <form action="<?= site_url('answer/delete/' . $answer['id_answer']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jawaban ini?<?= $isAdmin && !$isAnswerOwner ? " (Sebagai Admin)" : "" ?>');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus Jawaban</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="answer-comments-section mt-4">
                        <hr>
                        <h6>Komentar (<span id="comment-count-<?= $answer['id_answer'] ?>"><?= count($answer['comments']) ?></span>)</h6>

                        <div class="comment-list" id="comment-list-<?= $answer['id_answer'] ?>">
                            <?php
                            ?>
                            <?php if (!empty($answer['comments'])): ?>
                                <?php foreach ($answer['comments'] as $comment): ?>
                                    <div class="comment-item d-flex align-items-start mb-2"
                                        id="comment-item-<?= $comment['id_comment'] ?>"
                                        data-answer-id="<?= $answer['id_answer'] ?>">
                                        <img src="<?= base_url('assets/images/profiles/' . esc($comment['photo_profile'] ?? 'default.jpg')) ?>" alt="<?= esc($comment['nama_lengkap']) ?>" class="rounded-circle me-2" width="24" height="24">
                                        <div class="comment-content w-100">
                                            <strong><?= esc($comment['nama_lengkap']) ?></strong>

                                            <div id="comment-text-display-<?= $comment['id_comment'] ?>">
                                                <p class="mb-0"><?= esc($comment['comment_text']) ?></p>
                                                <small class="text-muted"><?= CodeIgniter\I18n\Time::parse($comment['created_at'])->humanize() ?></small>
                                            </div>

                                            <div class="comment-edit-form-area" id="comment-edit-form-area-<?= $comment['id_comment'] ?>" style="display: none;">
                                            </div>

                                            <?php
                                            $isCommentOwner = (session()->get('isLoggedIn') && session()->get('user_id') == $comment['id_user']);
                                            $isAdmin = (session()->get('isLoggedIn') && session()->get('role') == 'admin');
                                            ?>

                                            <?php if ($isCommentOwner || $isAdmin): ?>
                                                <div class="comment-actions mt-1">
                                                    <?php if ($isCommentOwner): 
                                                    ?>
                                                        <a href="#" class="btn-edit-comment small text-decoration-none" data-comment-id="<?= $comment['id_comment'] ?>">Edit</a>
                                                        ·
                                                    <?php endif; ?>

                                                    <?php
                                                    $isAdminDelete = $isAdmin && !$isCommentOwner;
                                                    ?>
                                                    <a href="#"
                                                        class="btn-delete-comment small text-decoration-none text-danger"
                                                        data-comment-id="<?= $comment['id_comment'] ?>"
                                                        data-is-admin-delete="<?= $isAdminDelete ? 'true' : 'false' ?>">
                                                        Hapus
                                                    </a>
                                                </div>
                                            <?php endif; ?>
            
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <?php if (session()->get('isLoggedIn')): ?>
                            <form class="comment-form mt-3" data-answer-id="<?= $answer['id_answer'] ?>" action="<?= site_url('comment/create/' . $answer['id_answer']) ?>" method="POST">
                                <div class="input-group">
                                    <textarea class="form-control form-control-sm" name="comment_text" placeholder="Tulis komentar..." rows="1" required></textarea>
                                    <button class="btn btn-outline-secondary btn-sm" type="submit">Kirim</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Belum ada jawaban untuk pertanyaan ini. Jadilah yang pertama menjawab!</p>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            Pertanyaan tidak ditemukan.
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>