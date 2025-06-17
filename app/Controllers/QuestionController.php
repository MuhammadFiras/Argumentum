<?php

namespace App\Controllers;

use App\Models\QuestionModel;
use App\Models\AnswerModel;
use App\Models\AnswerRatingModel;
use App\Models\AnswerCommentModel;
use App\Models\TopicModel;
use App\Models\QuestionTopicModel;

class QuestionController extends BaseController
{
    protected $questionModel;
    protected $answerModel;
    protected $answerRatingModel;
    protected $commentModel;
    protected $topicModel;
    protected $questionTopicModel;
    protected $helpers = ['form', 'url', 'text', 'date'];

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel();
        $this->answerRatingModel = new AnswerRatingModel();
        $this->commentModel = new AnswerCommentModel();
        $this->topicModel = new TopicModel();
        $this->questionTopicModel = new QuestionTopicModel();
    }

    public function view($slug = null)
    {
        if ($slug === null) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }
        $question = $this->questionModel->getQuestionWithDetails($slug);
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
            $answer['comments'] = $this->commentModel->getCommentsForAnswer($answer['id_answer']);

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

    public function ask()
    {
        $data = [
            'title' => 'Tanya Pertanyaan Baru',
            'validation' => \Config\Services::validation(),
            'topics' => $this->topicModel->findAll()
        ];
        return view('questions/ask_question', $data);
    }

    public function create()
    {
        $rules = [
            'title' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Judul pertanyaan wajib diisi.',
                    'max_length' => 'Judul pertanyaan maksimal {param} karakter.'
                ]
            ],
            'content' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Isi pertanyaan wajib diisi.',
                ]
            ],
            'topics'  => [
                'rules'  => 'required',
                'errors' => ['required' => 'Anda harus memilih setidaknya satu topik.']
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/ask')->withInput()->with('validation', $this->validator);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $slug = url_title($this->request->getPost('title'), '-', true);
        $originalSlug = $slug;
        $counter = 1;
        while ($this->questionModel->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $questionData = [
            'id_user' => session()->get('user_id'),
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'slug'    => $slug
        ];
        $this->questionModel->insert($questionData);

        $questionId = $this->questionModel->getInsertID();

        $topicIds = $this->request->getPost('topics');
        if (!empty($topicIds)) {
            $questionTopicData = [];
            foreach ($topicIds as $topicId) {
                $questionTopicData[] = [
                    'question_id' => $questionId,
                    'topic_id'    => $topicId
                ];
            }
            $this->questionTopicModel->insertBatch($questionTopicData);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal mempublikasikan pertanyaan karena kesalahan database.');
        } else {
            return redirect()->to('/')->with('success', 'Pertanyaan berhasil dipublikasikan!');
        }
    }

    public function edit($id_question)
    {
        $question = $this->questionModel->find($id_question);

        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        if (!session()->get('isLoggedIn') || $question['id_user'] != session()->get('user_id')) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki hak untuk mengedit pertanyaan ini.');
        }

        $existingTopicIds = $this->questionTopicModel
            ->where('question_id', $id_question)
            ->findColumn('topic_id');

        $data = [
            'title'           => 'Edit Pertanyaan: ' . esc($question['title']),
            'question'        => $question,
            'all_topics'      => $this->topicModel->findAll(),
            'existing_topics' => $existingTopicIds ?? [],
            'validation'      => session()->getFlashdata('validation') ?? \Config\Services::validation()
        ];
        return view('questions/edit_question', $data);
    }

    public function update($id_question)
    {
        $question = $this->questionModel->find($id_question);

        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        if (!session()->get('isLoggedIn') || $question['id_user'] != session()->get('user_id')) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki hak untuk mengupdate pertanyaan ini.');
        }

        $rules = [
            'title' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Judul pertanyaan wajib diisi.',
                    'max_length' => 'Judul pertanyaan maksimal {param} karakter.'
                ]
            ],
            'content' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Isi pertanyaan wajib diisi.',
                ]
            ],
            'topics'  => [
                'rules'  => 'required',
                'errors' => ['required' => 'Anda harus memilih setidaknya satu topik.']
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/questions/edit/' . $id_question)->withInput()->with('validation', $this->validator);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $newTitle = $this->request->getPost('title');
        $newSlug = $question['slug'];

        if ($newTitle != $question['title']) {
            $newSlug = url_title($newTitle, '-', true);
            $originalSlug = $newSlug;
            $counter = 1;
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

        $this->questionModel->update($id_question, $updateData);

        $this->questionTopicModel->where('question_id', $id_question)->delete();

        $topicIds = $this->request->getPost('topics');
        if (!empty($topicIds)) {
            $questionTopicData = [];
            foreach ($topicIds as $topicId) {
                $questionTopicData[] = [
                    'question_id' => $id_question,
                    'topic_id'    => $topicId
                ];
            }
            $this->questionTopicModel->insertBatch($questionTopicData);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/questions/edit/' . $id_question)->withInput()->with('error', 'Gagal memperbarui pertanyaan.');
        } else {
            return redirect()->to('/question/' . $newSlug)->with('success', 'Pertanyaan berhasil diperbarui!');
        }
    }

    public function delete($id_question)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Anda harus login untuk melakukan aksi ini.');
        }

        $question = $this->questionModel->find($id_question);

        if (!$question) {
            return redirect()->to('/')->with('error', 'Pertanyaan tidak ditemukan.');
        }

        $isOwner = ($question['id_user'] == session()->get('user_id'));
        $isAdmin = (session()->get('role') == 'admin');

        if (!($isOwner || $isAdmin)) {
            return redirect()->to('/question/' . $question['slug'])->with('error', 'Anda tidak memiliki hak untuk menghapus pertanyaan ini.');
        }

        if ($this->questionModel->delete($id_question)) {
            $message = 'Pertanyaan berhasil dihapus.';
            if ($isAdmin && !$isOwner) {
                $message = 'Pertanyaan (ID: ' . $id_question . ') berhasil dihapus oleh Admin.';
            }
            return redirect()->to('/')->with('success', $message);
        } else {
            return redirect()->to('/question/' . $question['slug'])->with('error', 'Gagal menghapus pertanyaan.');
        }
    }
}
