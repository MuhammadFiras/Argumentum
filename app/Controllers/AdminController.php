<?php

namespace App\Controllers;

use App\Models\AnswerCommentModel;
use App\Models\AnswerModel;
use App\Models\AnswerRatingModel;
use App\Models\QuestionModel;
use App\Models\QuestionTopicModel;
use App\Models\TopicModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
  protected $userModel;
  protected $questionsModel;
  protected $answersModel;
  protected $answerCommentsModel;
  protected $topicsModel;
  protected $answerRatingsModel;
  protected $questionTopicsModel;

  public function __construct()
  {
    $this->userModel = new UserModel();
    $this->questionsModel = new QuestionModel();
    $this->answersModel = new AnswerModel();
    $this->answerCommentsModel = new AnswerCommentModel();
    $this->topicsModel = new TopicModel();
    $this->answerRatingsModel = new AnswerRatingModel();
    $this->questionTopicsModel = new QuestionTopicModel();
    helper(['text', 'date']);
  }

  public function index()
  {
    $data = [
      'title' => 'Dashboard',
      'count' => [
        'user' => $this->userModel->getCountAllUsers(),
        'question' => $this->questionsModel->getCountAllQuestions(),
        'answer' => $this->answersModel->getCountAllAnswers(),
        'comment' => $this->answerCommentsModel->getCountAllComments()
      ]
    ];

    return view('admin/content/dashboard', $data);
  }

  public function usersTable()
  {
    $allUsers = $this->userModel->findAll();

    $data = [
      'title' => 'Users Table',
      'users' => $allUsers
    ];

    return view('admin/content/tables/users', $data);
  }

  public function questionsTable()
  {
    $allQuestions = $this->questionsModel->findAll();

    $data = [
      'title' => 'Quetions Table',
      'questions' => $allQuestions
    ];

    return view('admin/content/tables/questions', $data);
  }

  public function answersTable()
  {
    $allAnswers = $this->answersModel->findAll();

    $data = [
      'title' => 'Answers Table',
      'answers' => $allAnswers
    ];

    return view('admin/content/tables/answers', $data);
  }

  public function answerCommentsTable()
  {
    $allAnswerComments = $this->answerCommentsModel->findAll();

    $data = [
      'title' => 'Answer Comments Table',
      'answerComments' => $allAnswerComments
    ];

    return view('admin/content/tables/answer_comments', $data);
  }

  public function topicsTable()
  {
    $allTopics = $this->topicsModel->findAll();

    $data = [
      'title' => 'Topics Table',
      'topics' => $allTopics
    ];

    return view('admin/content/tables/topics', $data);
  }

  public function answerRatingsTable()
  {
    $allRatings = $this->answerRatingsModel->findAll();

    $data = [
      'title' => 'Answer Ratings Table',
      'ratings' => $allRatings
    ];

    return view('admin/content/tables/answer_ratings', $data);
  }

  public function questionTopicsTable()
  {
    $allPivot = $this->questionTopicsModel->findAll();

    $data = [
      'title' => 'Question Topics Table',
      'pivots' => $allPivot
    ];

    return view('admin/content/tables/question_topics', $data);
  }
}