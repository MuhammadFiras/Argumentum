<?php

namespace App\Controllers;

use App\Models\AnswerCommentModel;

class CommentController extends BaseController
{
    protected $commentModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        // Instansiasi model yang akan kita gunakan
        $this->commentModel = new AnswerCommentModel();
    }

    /**
     * Menerima data dari AJAX untuk membuat komentar baru pada sebuah jawaban.
     *
     * @param int $id_answer ID jawaban yang dikomentari
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function create(int $id_answer)
    {
        // 1. Keamanan: Pastikan request adalah POST dan user sudah login.
        //    Meskipun kita akan pasang filter di Routes, pengecekan di sini adalah lapisan tambahan.
        if ($this->request->getMethod() !== "POST") {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Metode tidak diizinkan.']);
        }
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda harus login untuk berkomentar.']);
        }

        // 2. Validasi Input
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
            // Jika validasi gagal, kirim pesan error dalam format JSON
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => $this->validator->getError('comment_text')
            ]);
        }

        // 3. Menyiapkan dan Menyimpan Data
        $data = [
            'id_answer'    => $id_answer,
            'id_user'      => session()->get('user_id'),
            'comment_text' => $this->request->getPost('comment_text')
        ];

        // Coba simpan ke database
        if ($this->commentModel->insert($data)) {
            // 4. Jika Berhasil: Siapkan dan Kirim Respons Sukses

            // Kita siapkan data balikan untuk ditampilkan oleh JavaScript tanpa perlu query lagi
            $newCommentData = [
                'comment_text'  => esc($data['comment_text']), // Langsung di-escape untuk keamanan
                'nama_lengkap'  => esc(session()->get('nama_lengkap')),
                'photo_profile' => esc(session()->get('photo_profile') ?? 'default.jpg'),
                'created_at'    => 'Baru saja' // Tampilan sederhana untuk komentar baru
            ];

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan.',
                'comment' => $newCommentData
            ]);
        } else {
            // 5. Jika Gagal menyimpan ke DB
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan internal, gagal menyimpan komentar.'
            ]);
        }
    }

    /**
     * Memproses permintaan AJAX untuk mengupdate sebuah komentar.
     *
     * @param int $id_comment ID dari komentar yang akan diupdate
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function update(int $id_comment)
    {
        // 1. Validasi Input
        $rules = [
            'comment_text' => 'required|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => $this->validator->getError('comment_text')
            ]);
        }

        // 2. Otorisasi: Cek apakah komentar ada & milik pengguna
        $comment = $this->commentModel->find($id_comment);
        if (!$comment) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Komentar tidak ditemukan.']);
        }

        // Hanya pemilik komentar atau admin yang boleh mengupdate
        $isOwner = ($comment['id_user'] == session()->get('user_id'));
        $isAdmin = (session()->get('role') == 'admin');

        if (!$isOwner && !$isAdmin) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengedit komentar ini.']);
        }

        // 3. Update Data
        $newText = $this->request->getPost('comment_text');
        $data = ['comment_text' => $newText];

        if ($this->commentModel->update($id_comment, $data)) {
            // Jika berhasil, kirim kembali teks yang sudah di-escape untuk ditampilkan
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Komentar berhasil diperbarui.',
                'updated_text' => esc($newText)
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal memperbarui komentar.']);
        }
    }

    /**
     * Memproses permintaan AJAX untuk menghapus sebuah komentar.
     *
     * @param int $id_comment ID dari komentar yang akan dihapus
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function delete(int $id_comment)
    {
        // 1. Otorisasi: Cek apakah komentar ada & milik pengguna
        $comment = $this->commentModel->find($id_comment);
        if (!$comment) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Komentar tidak ditemukan.']);
        }

        $isOwner = ($comment['id_user'] == session()->get('user_id'));
        $isAdmin = (session()->get('role') == 'admin');

        if (!$isOwner && !$isAdmin) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini.']);
        }

        // 2. Hapus Data
        if ($this->commentModel->delete($id_comment)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Komentar berhasil dihapus.']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menghapus komentar.']);
        }
    }
}
