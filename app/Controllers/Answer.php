<?php

namespace App\Controllers;

use App\Models\AnswerModel;
use App\Models\QuestionModel;
use App\Models\AnswerRatingModel;

class Answer extends BaseController
{
    protected $answerModel;
    protected $questionModel;
    protected $answerRatingModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->answerModel = new AnswerModel();
        $this->questionModel = new QuestionModel(); // Diperlukan untuk mendapatkan slug pertanyaan saat redirect
        $this->answerRatingModel = new AnswerRatingModel();
    }

    // ... (method submit(), rateAnswer(), toggleBestAnswer() tetap sama) ...
    public function submit($id_question)
    {
        // ... (kode submit jawaban yang sudah ada) ...
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

    public function rateAnswer(int $id_answer)
    {
        // ... (kode rate answer yang sudah ada) ...
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda harus login untuk memberi rating.']);
        }

        $ratingValue = $this->request->getPost('rating');
        $userId = session()->get('user_id');

        if (empty($ratingValue) || !is_numeric($ratingValue) || $ratingValue < 1 || $ratingValue > 5) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Nilai rating tidak valid (1-5).']);
        }

        $answerExists = $this->answerModel->find($id_answer);
        if (!$answerExists) {
             return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Jawaban tidak ditemukan.']);
        }

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
                'average_rating' => number_format($newRatingStats['average'], 1),
                'rating_count' => $newRatingStats['count']
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menyimpan rating. Silakan coba lagi.']);
        }
    }

    public function toggleBestAnswer(int $id_answer)
    {
        // ... (kode toggle best answer yang sudah ada) ...
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

    /**
     * Menampilkan form untuk mengedit jawaban.
     * @param int $id_answer
     */
    public function edit($id_answer)
    {
        $answer = $this->answerModel->find($id_answer);

        if (!$answer) {
            return redirect()->back()->with('error', 'Jawaban tidak ditemukan.');
        }

        // Otorisasi: Hanya pemilik jawaban yang boleh mengedit
        if (!session()->get('isLoggedIn') || $answer['id_user'] != session()->get('user_id')) {
            // Di masa depan, tambahkan pengecekan role admin di sini jika perlu
            // || session()->get('role') != 'admin'
            $question = $this->questionModel->find($answer['id_question']); // Untuk redirect kembali
            $slug = $question ? $question['slug'] : '';
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('error', 'Anda tidak memiliki hak untuk mengedit jawaban ini.');
        }

        // Ambil data pertanyaan untuk link "Kembali ke Pertanyaan"
        $question = $this->questionModel->find($answer['id_question']);

        $data = [
            'title' => 'Edit Jawaban',
            'answer' => $answer,
            'question' => $question, // Untuk link kembali
            'validation' => \Config\Services::validation()
        ];

        return view('answers/edit_answer', $data); // Kita akan buat view ini
    }

    /**
     * Memproses update jawaban.
     * @param int $id_answer
     */
    public function update($id_answer)
    {
        $answer = $this->answerModel->find($id_answer);

        if (!$answer) {
            return redirect()->back()->with('error', 'Jawaban tidak ditemukan.');
        }

        // Otorisasi: Hanya pemilik jawaban yang boleh mengupdate
        if (!session()->get('isLoggedIn') || $answer['id_user'] != session()->get('user_id')) {
            $question = $this->questionModel->find($answer['id_question']);
            $slug = $question ? $question['slug'] : '';
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('error', 'Anda tidak memiliki hak untuk mengupdate jawaban ini.');
        }

        $rules = [
            'answer_content' => [
                'rules' => 'required', // Validasi dasar, bisa ditambahkan min_length jika mau
                'errors' => [
                    'required' => 'Isi jawaban tidak boleh kosong.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('answer/edit/' . $id_answer)->withInput()->with('validation', $this->validator);
        }

        $updateData = [
            'content' => $this->request->getPost('answer_content')
        ];

        // Dapatkan slug pertanyaan untuk redirect
        $question = $this->questionModel->find($answer['id_question']);
        $slug = $question ? $question['slug'] : '';


        if ($this->answerModel->update($id_answer, $updateData)) {
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('success', 'Jawaban berhasil diperbarui.');
        } else {
            return redirect()->to('answer/edit/' . $id_answer)->withInput()->with('error', 'Gagal memperbarui jawaban.');
        }
    }

    /**
     * Menghapus jawaban.
     * @param int $id_answer
     */
    public function delete($id_answer)
    {
        $answer = $this->answerModel->find($id_answer);

        if (!$answer) {
            return redirect()->back()->with('error', 'Jawaban tidak ditemukan.');
        }

        // Otorisasi: Hanya pemilik jawaban yang boleh menghapus (atau admin nanti)
        if (!session()->get('isLoggedIn') || $answer['id_user'] != session()->get('user_id')) {
            // || session()->get('role') != 'admin'
            $question = $this->questionModel->find($answer['id_question']);
            $slug = $question ? $question['slug'] : '';
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('error', 'Anda tidak memiliki hak untuk menghapus jawaban ini.');
        }

        // Dapatkan slug pertanyaan untuk redirect
        $question = $this->questionModel->find($answer['id_question']);
        $slug = $question ? $question['slug'] : '';

        if ($this->answerModel->delete($id_answer)) {
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('success', 'Jawaban berhasil dihapus.');
        } else {
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('error', 'Gagal menghapus jawaban.');
        }
    }
}