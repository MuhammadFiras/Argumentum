<?php

namespace App\Controllers;

use App\Models\AnswerModel;
use App\Models\QuestionModel;
use App\Models\AnswerRatingModel; // <--- TAMBAHKAN USE STATEMENT

class Answer extends BaseController
{
    protected $answerModel;
    protected $questionModel;
    protected $answerRatingModel; // <--- TAMBAHKAN PROPERTY
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->answerModel = new AnswerModel();
        $this->questionModel = new QuestionModel();
        $this->answerRatingModel = new AnswerRatingModel(); // <--- INSTANSIASI MODEL
    }

    public function submit($id_question)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Anda harus login untuk menjawab.');
        }

        $rules = [
            'answer_content' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Isi jawaban tidak boleh kosong.',
                ]
            ]
        ];

        $question = $this->questionModel->find($id_question);
        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        if (!$this->validate($rules)) {
            return redirect()->to('question/' . $question['slug'])
                             ->withInput()
                             ->with('validation_answer', $this->validator);
        }

        $data = [
            'id_question' => $id_question,
            'id_user'     => session()->get('user_id'),
            'content'     => $this->request->getPost('answer_content')
        ];

        if ($this->answerModel->insert($data)) {
            return redirect()->to('question/' . $question['slug'])->with('success', 'Jawaban berhasil dikirim.');
        } else {
            return redirect()->to('question/' . $question['slug'])->withInput()->with('error', 'Gagal mengirim jawaban.');
        }
    }

    /**
     * Menerima dan menyimpan rating untuk sebuah jawaban via AJAX.
     * @param int $id_answer
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function rateAnswer(int $id_answer)
    {
        // Pastikan request adalah AJAX (opsional tapi baik untuk endpoint khusus AJAX)
        // if (!$this->request->isAJAX()) {
        //     return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        // }

        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda harus login untuk memberi rating.']);
        }

        $ratingValue = $this->request->getPost('rating');
        $userId = session()->get('user_id');

        // Validasi input rating
        if (empty($ratingValue) || !is_numeric($ratingValue) || $ratingValue < 1 || $ratingValue > 5) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Nilai rating tidak valid (1-5).']);
        }

        $answerExists = $this->answerModel->find($id_answer);
        if (!$answerExists) {
             return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Jawaban tidak ditemukan.']);
        }

        // User tidak bisa memberi rating pada jawabannya sendiri
        if ($answerExists['id_user'] == $userId) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak bisa memberi rating pada jawaban sendiri.']);
        }

        $data = [
            'id_answer' => $id_answer,
            'id_user'   => $userId,
            'rating'    => (int)$ratingValue
        ];

        if ($this->answerRatingModel->saveRating($data)) {
            $newRatingStats = $this->answerRatingModel->getAverageRating($id_answer);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rating berhasil disimpan!',
                'average_rating' => number_format($newRatingStats['average'], 1), // Format untuk tampilan
                'rating_count' => $newRatingStats['count']
            ]);
        } else {
            // Error ini mungkin terjadi jika ada masalah database atau unique constraint gagal dihandle (seharusnya tidak jika logic saveRating benar)
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menyimpan rating. Silakan coba lagi.']);
        }
    }
    // TODO: Implementasi edit, update, delete answer

            /**
     * Menandai atau membatalkan status "Jawaban Terbaik" untuk sebuah jawaban.
     * @param int $id_answer
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function toggleBestAnswer(int $id_answer) 
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Anda harus login untuk melakukan aksi ini.');
        }

        $answer = $this->answerModel->find($id_answer);
        if (!$answer) {
            return redirect()->back()->with('error', 'Jawaban tidak ditemukan.');
        }

        $question = $this->questionModel->find($answer['id_question']);
        if (!$question) {
            return redirect()->back()->with('error', 'Pertanyaan terkait tidak ditemukan.');
        }

        if ($question['id_user'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk mengubah status jawaban ini.');
        }

        if ($answer['is_best_answer']) {
            if ($this->answerModel->unmarkAsBest($id_answer)) {
                return redirect()->to('question/' . $question['slug'])->with('success', 'Status jawaban terbaik berhasil dibatalkan.');
            }
        } else {
            if ($this->answerModel->markAsBest($id_answer, $question['id_question'])) {
                return redirect()->to('question/' . $question['slug'])->with('success', 'Jawaban berhasil ditandai sebagai yang terbaik!');
            }
        }

        return redirect()->to('question/' . $question['slug'])->with('error', 'Gagal mengubah status jawaban.');
    }
}