<?php

namespace App\Controllers;

use App\Models\QuestionModel;

class Home extends BaseController
{
    protected $questionModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        helper(['text', 'date']);
    }

    public function index(): string
    {
        $data = [
            'title' => 'Argumentum',
            'questions' => $this->questionModel->getQuestionsWithUser()
        ];
        return view('home', $data);
    }
}