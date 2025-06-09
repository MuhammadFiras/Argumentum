<?php

namespace App\Controllers;

use App\Models\AnswerModel;
use App\Models\QuestionModel;

class Home extends BaseController
{
    protected $questionModel;
    protected $answerModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        helper(['text', 'date']);

        $this->answerModel = new AnswerModel();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Argumentum',
            'section' => 'home',
            'header' => 'Daftar Pertanyaan',
            'questions' => $this->questionModel->getQuestionsWithUser()
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