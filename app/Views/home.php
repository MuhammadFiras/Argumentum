<?= $this->extend('layout/main_layout') ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-down">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-down">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3" data-aos="fade-right">
        <h1 class="text-danger fw-bold"><?= esc($header); ?></h1>
        <?php if (session()->get('isLoggedIn')): ?>
            <a href="<?= site_url('/ask') ?>" class="btn btn-danger btn-sm shadow-sm">+ Tanya Pertanyaan</a>
        <?php endif; ?>
    </div>

    <?php if ($section == 'home' && !empty($all_topics)): ?>
        <div class="topic-filter-container mb-4" data-aos="fade-left">
            <form action="<?= site_url('/') ?>" method="get" id="topicFilterForm">
                <div class="row align-items-center g-2">
                    <div class="col-auto">
                        <label for="topic_id_filter" class="col-form-label">Filter Topik:</label>
                    </div>
                    <div class="col-auto">
                        <select name="topic_id" id="topic_id_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Topik</option>
                            <?php foreach ($all_topics as $topic): ?>
                                <option value="<?= esc($topic['id']) ?>" <?= ($current_topic_id == $topic['id']) ? 'selected' : '' ?>>
                                    <?= esc($topic['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if (!empty($questions) && is_array($questions) && $section != 'my_answers'): ?>
        <?php foreach ($questions as $question): ?>
            <div class="mb-3" data-aos="fade-up" data-aos-delay="<?= rand(0, 300) ?>">
                <div class="card question-card shadow-sm border-danger rounded-3">
                    <div class="card-body">
                        <h5 class="card-title question-title">
                            <a href="<?= site_url('question/' . esc($question['slug'], 'url')) ?>" class="text-decoration-none text-dark fw-semibold">
                                <?= esc($question['title']) ?>
                            </a>
                        </h5>
                        <p class="card-text text-secondary">
                            <?= word_limiter(esc($question['content']), 30) ?>
                        </p>

                        <?php if (!empty($question['topics'])): ?>
                            <div class="topics-container">
                                <?php
                                $topic_array = explode(', ', $question['topics']);
                                ?>
                                <?php foreach ($topic_array as $topic_name): ?>
                                    <span class="badge bg-secondary me-1"><?= esc($topic_name) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="user-info text-muted fst-italic small mt-1">
                            Ditanyakan oleh: <?= esc($question['user_nama']) ?> <br>
                            <small><?= CodeIgniter\I18n\Time::parse($question['created_at'])->humanize() ?></small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif ($section != 'my_answers'): ?>
        <p class="text-center fst-italic text-secondary" data-aos="fade-in">Belum ada pertanyaan.</p>
    <?php endif; ?>

    <?php if (!empty($answers_by_user) && $section == 'my_answers'): ?>
        <?php foreach ($answers_by_user as $answer): ?>
            <div class="mb-3" data-aos="fade-up" data-aos-delay="<?= rand(0, 300) ?>">
                <div class="card question-card shadow-sm border-danger rounded-3">
                    <div class="card-body">
                        <div class="list-item-title">
                            <a href="<?= site_url('question/' . esc($answer['question_slug']) . '#answer-' . $answer['id_answer']) ?>">
                                Jawaban untuk: <?= esc($answer['question_title']) ?>
                            </a>
                        </div>
                        <p class="list-item-content"><?= word_limiter(esc($answer['content']), 30) ?></p>
                        <small class="text-muted">Diberikan: <?= CodeIgniter\I18n\Time::parse($answer['created_at'])->toLocalizedString('d MMMM yyyy') ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </ul>
    <?php elseif ($section == 'my_answers'): ?>
        <p class="text-center fst-italic text-secondary">Belum ada jawaban.</p>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>