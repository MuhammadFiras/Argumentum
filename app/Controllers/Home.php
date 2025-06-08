<?php

namespace App\Controllers;

use App\Models\QuestionModel;

class Home extends BaseController
{
    protected $questionModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        helper(['text', 'date']); // Load helper untuk format tanggal dan teks
    }

    public function index(): string
    {
        $data = [
            'title' => 'Argumentum',
            'questions' => $this->questionModel->getQuestionsWithUser() // Ambil semua pertanyaan dengan info user
        ];
        return view('home', $data); // Kita akan buat view 'home.php'
    }
}