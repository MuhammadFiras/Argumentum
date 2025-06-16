<?php

namespace App\Controllers;

use App\Models\AnswerModel;
use App\Models\QuestionModel;
use App\Models\TopicModel;

class Home extends BaseController
{
    protected $questionModel;
    protected $answerModel;
    protected $topicModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel();
        $this->topicModel = new TopicModel();
        helper(['text', 'date']);
    }

    public function index(): string
    {
        // 1. Ambil ID topik dari URL query string (misal: /?topic_id=1)
        $topicId = $this->request->getGet('topic_id');

        if (empty($topicId)) {
            $topicId = null;
        }

        // 2. Ambil pertanyaan (sudah difilter jika topicId ada)
        $questions = $this->questionModel->getAllQuestionsWithDetails($topicId);

        // 3. Ambil semua topik untuk ditampilkan sebagai tombol filter
        $allTopics = $this->topicModel->findAll();

        $data = [
            'title'          => 'Argumentum',
            'section'        => 'home',
            'header'         => 'Daftar Pertanyaan',
            'questions'      => $questions,
            'all_topics'     => $allTopics, // Kirim semua topik ke view
            'current_topic_id' => $topicId    // Kirim ID filter yang aktif ke view
        ];
        return view('home', $data);
    }

    public function myQuestions()
    {
        $userId = session()->get('user_id');
        $myQuestions = $this->questionModel->getQuestionsByUserId($userId);

        $data = [
            'title' => 'Argumentum',
            'section' => 'my_questions',
            'header' => 'Pertanyaan Saya',
            'questions' => $myQuestions
        ];
        return view('home', $data);
    }

    public function myAnswers()
    {
        $userId = session()->get('user_id');
        $answersByUser = $this->answerModel->getAnswersByUserId($userId);

        $data = [
            'title' => 'Argumentum',
            'section' => 'my_answers',
            'header' => 'Pertanyaan Yang Dijawab',
            'answers_by_user' => $answersByUser
        ];
        return view('home', $data);
    }
}
