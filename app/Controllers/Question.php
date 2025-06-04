<?php

namespace App\Controllers;

use App\Models\QuestionModel;
use App\Models\AnswerModel;
use App\Models\AnswerRatingModel; // Pastikan use statement ini ada

class Question extends BaseController
{
    protected $questionModel;
    protected $answerModel;
    protected $answerRatingModel; // Property untuk model rating
    protected $helpers = ['form', 'url', 'text', 'date'];

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel();
        $this->answerRatingModel = new AnswerRatingModel(); // Instansiasi model rating
    }

    // Menampilkan form untuk bertanya
    public function ask()
    {
        $data = [
            'title' => 'Tanya Pertanyaan Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('questions/ask_question', $data);
    }

    // Memproses pertanyaan yang disubmit
    public function create()
    {
        $rules = [
            'title' => [
                'rules' => 'required|min_length[10]|max_length[255]',
                'errors' => [
                    'required' => 'Judul pertanyaan wajib diisi.',
                    'min_length' => 'Judul pertanyaan minimal {param} karakter.',
                    'max_length' => 'Judul pertanyaan maksimal {param} karakter.'
                ]
            ],
            'content' => [
                'rules' => 'required|min_length[20]',
                'errors' => [
                    'required' => 'Isi pertanyaan wajib diisi.',
                    'min_length' => 'Isi pertanyaan minimal {param} karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/ask')->withInput()->with('validation', $this->validator);
        }

        $slug = url_title($this->request->getPost('title'), '-', true);
        $originalSlug = $slug;
        $counter = 1;
        while ($this->questionModel->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data = [
            'id_user' => session()->get('user_id'),
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'slug'    => $slug
        ];

        if ($this->questionModel->insert($data)) {
            return redirect()->to('/')->with('success', 'Pertanyaan berhasil dipublikasikan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mempublikasikan pertanyaan.');
        }
    }

    // Menampilkan detail pertanyaan dan jawabannya
    public function view($slug = null)
    {
        if ($slug === null) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        $question = $this->questionModel->getQuestionsWithUser($slug);

        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan atau slug tidak valid.');
        }

        $answersFromDB = $this->answerModel->getAnswersForQuestion($question['id_question']);
        $processedAnswers = [];

        $loggedInUserId = session()->get('isLoggedIn') ? session()->get('user_id') : null;

        foreach ($answersFromDB as $answer) {
            // 1. Dapatkan statistik rating (rata-rata dan jumlah)
            $answer['rating_stats'] = $this->answerRatingModel->getAverageRating($answer['id_answer']);

            // 2. Dapatkan rating yang diberikan oleh user yang sedang login (jika ada)
            $userGivenRatingValue = 0;
            if ($loggedInUserId) {
                $userSpecificRating = $this->answerRatingModel->getRatingByUser($answer['id_answer'], $loggedInUserId);
                if ($userSpecificRating) {
                    $userGivenRatingValue = (int)$userSpecificRating['rating'];
                }
            }
            $answer['user_given_rating'] = $userGivenRatingValue;

            $processedAnswers[] = $answer;
        }

        $data = [
            'title' => esc($question['title']),
            'question' => $question,
            'answers' => $processedAnswers,
            'validation_answer' => session()->getFlashdata('validation_answer') ?? \Config\Services::validation()
        ];

        return view('questions/view_question', $data);
    }

    // TODO: Implementasi edit, update, delete question
}