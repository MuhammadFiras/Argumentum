<?php

namespace App\Controllers;

use App\Models\AnswerModel;
use App\Models\QuestionModel; // Untuk mendapatkan slug pertanyaan setelah submit jawaban

class Answer extends BaseController
{
    protected $answerModel;
    protected $questionModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->answerModel = new AnswerModel();
        $this->questionModel = new QuestionModel();
    }

    public function submit($id_question)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Anda harus login untuk menjawab.');
        }

        $rules = [
            'answer_content' => [
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'Isi jawaban tidak boleh kosong.',
                    'min_length' => 'Jawaban minimal {param} karakter.'
                ]
            ]
        ];

        $question = $this->questionModel->find($id_question);
        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        if (!$this->validate($rules)) {
            // Redirect kembali ke halaman pertanyaan dengan error validasi
            // Menyimpan error validasi ke flashdata agar bisa diakses di view_question.php
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

    // TODO: Implementasi edit, update, delete answer
}