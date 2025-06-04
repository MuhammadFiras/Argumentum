<?php

namespace App\Controllers;

use App\Models\QuestionModel;
use App\Models\AnswerModel; 

class Question extends BaseController
{
    protected $questionModel;
    protected $answerModel; // Untuk nanti
    protected $helpers = ['form', 'url', 'text']; // Tambahkan helper text untuk slugify

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel(); // Untuk nanti
    }

    // Menampilkan form untuk bertanya
    public function ask()
    {
        $data = [
            'title' => 'Tanya Pertanyaan Baru',
            'validation' => \Config\Services::validation() // Kirim service validasi ke view
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
            // Kembali ke form dengan error dan input lama
            // Cara pertama: return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            // Cara kedua (agar service validation bisa diakses di view dengan lebih mudah):
            return redirect()->to('/ask')->withInput()->with('validation', $this->validator);
        }

        // Buat slug dari judul
        $slug = url_title($this->request->getPost('title'), '-', true); // Dari helper 'text'
        // Pastikan slug unik (bisa ditambahkan loop dengan suffix angka jika sudah ada)
        // Untuk sementara, kita asumsikan unik atau bisa dihandle oleh constraint UNIQUE di DB
        // Implementasi cek slug unik yang lebih baik:
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
            // throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan atau slug tidak valid.');
        }

        $data = [
            'title' => esc($question['title']),
            'question' => $question,
            'answers' => $this->answerModel->getAnswersForQuestion($question['id_question'])
        ];

        return view('questions/view_question', $data);
    }

    // TODO: Implementasi edit, update, delete question
}