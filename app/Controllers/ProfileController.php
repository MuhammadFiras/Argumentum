<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\QuestionModel; // Untuk menampilkan pertanyaan user
use App\Models\AnswerModel;   // Untuk menampilkan jawaban user (opsional untuk sekarang)

class ProfileController extends BaseController
{
    protected $userModel;
    protected $questionModel;
    protected $answerModel;
    protected $helpers = ['form', 'url', 'text', 'date'];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel();
    }

    /**
     * Menampilkan halaman profil pengguna.
     * Jika $id_user null, tampilkan profil user yang login.
     * Jika $id_user ada, tampilkan profil user dengan ID tersebut.
     */
    public function view($id_user = null)
    {
        $targetUserId = $id_user;
        $isOwnProfile = false;

        if ($targetUserId === null) {
            // Jika tidak ada ID, coba tampilkan profil user yang login
            if (!session()->get('isLoggedIn')) {
                return redirect()->to('/login')->with('error', 'Anda harus login untuk melihat profil Anda.');
            }
            $targetUserId = session()->get('user_id');
            $isOwnProfile = true;
        }

        $user = $this->userModel->find($targetUserId);

        if (!$user) {
            // throw PageNotFoundException::forPageNotFound();
            return redirect()->to('/')->with('error', 'Profil pengguna tidak ditemukan.');
        }

        // Ambil pertanyaan yang dibuat oleh pengguna ini
        $questions = $this->questionModel->getQuestionsByUserId($targetUserId);
        // Ambil jawaban yang diberikan oleh pengguna ini
        $answersByUser = $this->answerModel->getAnswersByUserId($targetUserId); // <--- AMBIL JAWABAN

        $data = [
            'title' => 'Profil ' . esc($user['nama_lengkap']),
            'user_profile' => $user, // Data pengguna yang akan ditampilkan
            'questions_by_user' => $questions,
            'answers_by_user' => $answersByUser, // Jika sudah ada
            'is_own_profile' => $isOwnProfile // Flag untuk menandakan apakah ini profil sendiri
        ];

        return view('profile/view_profile', $data); // Kita akan buat view ini
    }

    /**
     * Menampilkan form untuk mengedit profil user yang sedang login.
     */
    public function edit()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Anda harus login untuk mengedit profil.');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            // Seharusnya tidak terjadi jika user sudah login dengan benar
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Data pengguna tidak ditemukan. Silakan login kembali.');
        }

        $data = [
            'title' => 'Edit Profil Saya',
            'user_data' => $user,
            'validation' => \Config\Services::validation()
        ];
        return view('profile/edit_profile', $data); // Kita akan buat view ini
    }

    /**
     * Memproses update profil user yang sedang login.
     */
    public function update()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $userId = session()->get('user_id');

        // Aturan Validasi
        $rules = [
            'nama_lengkap' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.',
                ]
            ],
            'description' => 'permit_empty|max_length[500]', 
            'credentials' => 'permit_empty|max_length[250]',
            'linkedin_url' => 'permit_empty|max_length[255]',
            'instagram_url' => 'permit_empty|max_length[255]',
            // 'photo_profile' => [ // Validasi untuk upload gambar (jika diimplementasikan)
            //     'rules' => 'uploaded[photo_profile]|max_size[photo_profile,1024]|is_image[photo_profile]|mime_in[photo_profile,image/jpg,image/jpeg,image/png]',
            //     'errors' => [ /* ... pesan error upload ... */ ]
            // ]
        ];

        // Tambahkan aturan validasi email jika email diizinkan untuk diubah
        // if ($this->request->getPost('email') != $user['email']) {
        //     $rules['email'] = 'required|valid_email|is_unique[users.email,id_user,'.$userId.']';
        // } else {
        //     $rules['email'] = 'required|valid_email';
        // }

        if (!$this->validate($rules)) {
            return redirect()->to('/profile/edit')->withInput()->with('validation', $this->validator);
        }

        $updateData = [
            'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
            'description'   => $this->request->getPost('description'),
            'credentials'   => $this->request->getPost('credentials'),
            'linkedin_url'  => $this->request->getPost('linkedin_url'),
            'instagram_url' => $this->request->getPost('instagram_url'),
        ];

        // Handle Upload Foto Profil (Jika ada field input 'photo_profile')
        // $imgFile = $this->request->getFile('photo_profile');
        // if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
        //     $newName = $imgFile->getRandomName();
        //     $imgFile->move(WRITEPATH . '../public/assets/images/profiles/', $newName); // Sesuaikan path
        //     $updateData['photo_profile'] = $newName;
        //     // Hapus foto lama jika bukan default.jpg
        //     if ($user['photo_profile'] && $user['photo_profile'] != 'default.jpg') {
        //         @unlink(WRITEPATH . '../public/assets/images/profiles/' . $user['photo_profile']);
        //     }
        // }

        if ($this->userModel->update($userId, $updateData)) {
            // Update session jika nama lengkap berubah
            if (session()->get('nama_lengkap') != $updateData['nama_lengkap']) {
                session()->set('nama_lengkap', $updateData['nama_lengkap']);
            }
            // if (isset($updateData['photo_profile'])) {
            //     session()->set('photo_profile', $updateData['photo_profile']);
            // }
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
        } else {
            return redirect()->to('/profile/edit')->withInput()->with('error', 'Gagal memperbarui profil.');
        }
    }
}