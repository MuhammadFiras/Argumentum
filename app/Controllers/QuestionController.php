<?php

namespace App\Controllers;

use App\Models\QuestionModel;
use App\Models\AnswerModel;
use App\Models\AnswerRatingModel;

class QuestionController extends BaseController
{
    protected $questionModel;
    protected $answerModel;
    protected $answerRatingModel;
    protected $helpers = ['form', 'url', 'text', 'date'];

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel();
        $this->answerRatingModel = new AnswerRatingModel();
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
        // ... (kode create tetap sama) ...
        $rules = [
            'title' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Judul pertanyaan wajib diisi.',
                ]
            ],
            'content' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi pertanyaan wajib diisi.',
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
        // ... (kode view tetap sama) ...
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
            $answer['rating_stats'] = $this->answerRatingModel->getAverageRating($answer['id_answer']);
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

    /**
     * Menampilkan form untuk mengedit pertanyaan.
     * @param int $id_question
     */
    public function edit($id_question)
    {
        $question = $this->questionModel->find($id_question);

        // Cek apakah pertanyaan ada
        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        // Otorisasi: Pastikan user yang login adalah pemilik pertanyaan
        if (!session()->get('isLoggedIn') || $question['id_user'] != session()->get('user_id')) {
            // Atau bisa juga admin di masa depan: || session()->get('role') != 'admin'
            return redirect()->to('/')->with('error', 'Anda tidak memiliki hak untuk mengedit pertanyaan ini.');
        }

        $data = [
            'title' => 'Edit Pertanyaan: ' . esc($question['title']),
            'question' => $question,
            'validation' => \Config\Services::validation() // Untuk menampilkan error validasi jika ada dari proses update
        ];
        return view('questions/edit_question', $data); // Kita akan buat view ini
    }

    /**
     * Memproses update pertanyaan.
     * @param int $id_question
     */
    public function update($id_question)
    {
        $question = $this->questionModel->find($id_question);

        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        // Otorisasi
        if (!session()->get('isLoggedIn') || $question['id_user'] != session()->get('user_id')) {
            // Atau admin
            return redirect()->to('/')->with('error', 'Anda tidak memiliki hak untuk mengupdate pertanyaan ini.');
        }

         // Salin pesan error dari method create untuk keringkasan
        $rules['title']['errors'] = [
            'required' => 'Judul pertanyaan wajib diisi.',
        ];
        $rules['content']['errors'] = [
            'required' => 'Isi pertanyaan wajib diisi.',
        ];


        if (!$this->validate($rules)) {
            // Kembali ke form edit dengan error dan input lama
            return redirect()->to('/questions/edit/' . $id_question)->withInput()->with('validation', $this->validator);
        }

        $newTitle = $this->request->getPost('title');
        $newSlug = $question['slug']; // Default ke slug lama

        // Jika judul berubah, buat slug baru dan pastikan unik (kecuali untuk dirinya sendiri)
        if ($newTitle != $question['title']) {
            $newSlug = url_title($newTitle, '-', true);
            $originalSlug = $newSlug;
            $counter = 1;
            // Cek apakah slug baru sudah ada dan bukan milik pertanyaan ini sendiri
            $existingQuestionWithSlug = $this->questionModel->where('slug', $newSlug)->where('id_question !=', $id_question)->first();
            while ($existingQuestionWithSlug) {
                $newSlug = $originalSlug . '-' . $counter;
                $counter++;
                $existingQuestionWithSlug = $this->questionModel->where('slug', $newSlug)->where('id_question !=', $id_question)->first();
            }
        }

        $updateData = [
            'title'   => $newTitle,
            'content' => $this->request->getPost('content'),
            'slug'    => $newSlug
        ];

        if ($this->questionModel->update($id_question, $updateData)) {
            return redirect()->to('/question/' . $newSlug)->with('success', 'Pertanyaan berhasil diperbarui!');
        } else {
            return redirect()->to('/questions/edit/' . $id_question)->withInput()->with('error', 'Gagal memperbarui pertanyaan.');
        }
    }

    /**
     * Menghapus pertanyaan.
     * @param int $id_question
     */
    public function delete($id_question)
    {
        // Pastikan method adalah POST (jika rute adalah POST)
        // if (!$this->request->is('post')) {
        //     return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        // }

        $question = $this->questionModel->find($id_question);

        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        // Otorisasi: Pastikan user yang login adalah pemilik pertanyaan (atau admin nanti)
        if (!session()->get('isLoggedIn') || $question['id_user'] != session()->get('user_id')) {
            // Atau admin
            return redirect()->to('/')->with('error', 'Anda tidak memiliki hak untuk menghapus pertanyaan ini.');
        }

        // Hapus juga jawaban terkait (CASCADE ON DELETE di DB seharusnya menangani ini,
        // tapi bisa juga dilakukan secara manual jika perlu logika tambahan)
        // $this->answerModel->where('id_question', $id_question)->delete();

        if ($this->questionModel->delete($id_question)) {
            return redirect()->to('/')->with('success', 'Pertanyaan berhasil dihapus.');
        } else {
            return redirect()->to('/question/' . $question['slug'])->with('error', 'Gagal menghapus pertanyaan.');
        }
    }
}