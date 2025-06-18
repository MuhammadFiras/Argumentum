<?php

namespace App\Controllers;

use App\Models\AnswerModel;
use App\Models\QuestionModel;
use App\Models\AnswerRatingModel;

class AnswerController extends BaseController
{
    protected $answerModel;
    protected $questionModel;
    protected $answerRatingModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->answerModel = new AnswerModel();
        $this->questionModel = new QuestionModel();
        $this->answerRatingModel = new AnswerRatingModel();
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

    public function rateAnswer(int $id_answer)
    {
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

    public function edit($id_answer)
    {
        $answer = $this->answerModel->find($id_answer);

        if (!$answer) {
            return redirect()->back()->with('error', 'Jawaban tidak ditemukan.');
        }

        if (!session()->get('isLoggedIn') || $answer['id_user'] != session()->get('user_id')) {
            $question = $this->questionModel->find($answer['id_question']);
            $slug = $question ? $question['slug'] : '';
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('error', 'Anda tidak memiliki hak untuk mengedit jawaban ini.');
        }

        $question = $this->questionModel->find($answer['id_question']);

        $data = [
            'title' => 'Edit Jawaban',
            'answer' => $answer,
            'question' => $question,
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation()
        ];

        return view('answers/edit_answer', $data);
    }

    public function update($id_answer)
    {
        $answer = $this->answerModel->find($id_answer);

        if (!$answer) {
            return redirect()->back()->with('error', 'Jawaban tidak ditemukan.');
        }

        if (!session()->get('isLoggedIn') || $answer['id_user'] != session()->get('user_id')) {
            $question = $this->questionModel->find($answer['id_question']);
            $slug = $question ? $question['slug'] : '';
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('error', 'Anda tidak memiliki hak untuk mengupdate jawaban ini.');
        }

        $rules = [
            'answer_content' => [
                'rules' => 'required',
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

        $question = $this->questionModel->find($answer['id_question']);
        $slug = $question ? $question['slug'] : '';

        if ($this->answerModel->update($id_answer, $updateData)) {
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('success', 'Jawaban berhasil diperbarui.');
        } else {
            return redirect()->to('answer/edit/' . $id_answer)->withInput()->with('error', 'Gagal memperbarui jawaban.');
        }
    }


    public function delete($id_answer)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Anda harus login untuk melakukan aksi ini.');
        }

        $answer = $this->answerModel->find($id_answer);

        if (!$answer) {
            return redirect()->back()->with('error', 'Jawaban tidak ditemukan.');
        }

        $isOwner = ($answer['id_user'] == session()->get('user_id'));
        $isAdmin = (session()->get('role') == 'admin');

        if (!($isOwner || $isAdmin)) {
            $question = $this->questionModel->find($answer['id_question']);
            $slug = $question ? $question['slug'] : '';
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('error', 'Anda tidak memiliki hak untuk menghapus jawaban ini.');
        }

        $question = $this->questionModel->find($answer['id_question']);
        $slug = $question ? $question['slug'] : '';

        if ($this->answerModel->delete($id_answer)) {
            $message = 'Jawaban berhasil dihapus.';
            if ($isAdmin && !$isOwner) {
                $message = 'Jawaban (ID: ' . $id_answer . ') berhasil dihapus oleh Admin.';
            }
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('success', $message);
        } else {
            return redirect()->to($slug ? 'question/' . $slug : '/')->with('error', 'Gagal menghapus jawaban.');
        }
    }

    public function deleteRating(int $id_answer)
    {
        $userId = session()->get('user_id');

        $deleted = $this->answerRatingModel->deleteRatingByUser($id_answer, $userId);

        if ($deleted) {
            $newStats = $this->answerRatingModel->getAverageRating($id_answer);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rating Anda telah dihapus.',
                'average_rating' => number_format($newStats['average'], 1),
                'rating_count' => $newStats['count']
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Rating tidak ditemukan untuk dihapus.'
            ]);
        }
    }
}
