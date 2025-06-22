<?php

namespace App\Controllers;

use App\Models\AnswerCommentModel;

class CommentController extends BaseController
{
    protected $commentModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->commentModel = new AnswerCommentModel();
    }

    public function create(int $id_answer)
    {
        if ($this->request->getMethod() !== "POST") {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Metode tidak diizinkan.']);
        }
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda harus login untuk berkomentar.']);
        }

        $rules = [
            'comment_text' => [
                'rules' => 'required|max_length[1000]',
                'errors' => [
                    'required' => 'Komentar tidak boleh kosong.',
                    'max_length' => 'Komentar tidak boleh lebih dari 1000 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => $this->validator->getError('comment_text')
            ]);
        }

        $config = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($config);

        $commentText = $this->request->getPost('comment_text');
        $sanitizedCommentText = $purifier->purify($commentText);

        $data = [
            'id_answer'    => $id_answer,
            'id_user'      => session()->get('user_id'),
            'comment_text' => $sanitizedCommentText
        ];

        if ($this->commentModel->insert($data)) {
            $newCommentData = [
                'comment_text'  => esc($data['comment_text']),
                'nama_lengkap'  => esc(session()->get('nama_lengkap')),
                'photo_profile' => esc(session()->get('photo_profile') ?? 'default.jpg'),
                'created_at'    => 'Baru saja'
            ];

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan.',
                'comment_id' => $this->commentModel->getInsertID(),
                'comment' => $newCommentData
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan internal, gagal menyimpan komentar.'
            ]);
        }
    }

    public function update(int $id_comment)
    {
        $rules = [
            'comment_text' => 'required|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => $this->validator->getError('comment_text')
            ]);
        }

        $comment = $this->commentModel->find($id_comment);
        if (!$comment) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Komentar tidak ditemukan.']);
        }

        $isOwner = ($comment['id_user'] == session()->get('user_id'));
        $isAdmin = (session()->get('role') == 'admin');

        if (!$isOwner && !$isAdmin) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengedit komentar ini.']);
        }

        $config = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($config);

        $newText = $this->request->getPost('comment_text');
        $sanitizedNewText = $purifier->purify($newText);

        $data = ['comment_text' => $sanitizedNewText];

        if ($this->commentModel->update($id_comment, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Komentar berhasil diperbarui.',
                'updated_text' => esc($sanitizedNewText)
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal memperbarui komentar.']);
        }
    }

    public function delete(int $id_comment)
    {
        $comment = $this->commentModel->find($id_comment);
        if (!$comment) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Komentar tidak ditemukan.']);
        }

        $isOwner = ($comment['id_user'] == session()->get('user_id'));
        $isAdmin = (session()->get('role') == 'admin');

        if (!$isOwner && !$isAdmin) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini.']);
        }

        if ($this->commentModel->delete($id_comment)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Komentar berhasil dihapus.']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menghapus komentar.']);
        }
    }
}
