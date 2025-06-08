<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\QuestionModel; // Untuk menampilkan pertanyaan user
use App\Models\AnswerModel;   // Untuk menampilkan jawaban user (opsional untuk sekarang)
use CodeIgniter\Validation\StrictRules\Rules;

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
            'validation' => \Config\Services::validation() // <--- TAMBAHKAN BARIS INI
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
        $user = $this->userModel->find($userId);

        // Aturan Validasi
        $rules = [
            'nama_lengkap' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.',
                    'min_length' => 'Nama lengkap minimal {param} karakter.',
                    'max_length' => 'Nama lengkap maksimal {param} karakter.',                ]
            ],
            'description' => 'max_length[500]',
            'credentials' => 'max_length[250]',
            'linkedin_url' => [
                'rules' => 'max_length[255]|valid_url',
                'errors' => [
                    'max_length' => "Karakter dalam Link melebihi 255 karakter",
                    'valid_url' => 'Link tidak valid',
                ]
            ],
            'instagram_url' => [
                'rules' => 'max_length[255]|valid_url',
                'errors' => [
                    'max_length' => "Karakter dalam Link melebihi 255 karakter",
                    'valid_url' => 'Link tidak valid',
                ]
            ],
            'photo_profile' => [
                'rules' => 'max_size[photo_profile,1024]|is_image[photo_profile]|mime_in[photo_profile,image/png,image/jpeg,image/jpg]',
                'errors' => [
                    'max_size' => 'Ukuran foto tidak boleh lebih dari 1 MB.',
                    'is_image' => 'Format file tidak valid. Hanya izinkan PNG, JPG, JPEG.',
                    'mime_in'  => 'Format file tidak valid. Hanya izinkan PNG, JPG, JPEG.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $photoProfileName = $user['photo_profile']; // Nama file asal
        $photoProfileFile = $this->request->getFile('photo_profile');
        // dd($photoProfileFile);

        // Cek apakah ada file yang diunggah, valid, dan belum dipindahkan
        if ($photoProfileFile && $photoProfileFile->isValid() && !$photoProfileFile->hasMoved()) {
            $newName = $photoProfileFile->getRandomName(); // Buat nama file acak yang aman

            // Tentukan path tujuan
            $targetPath = FCPATH . 'assets/images/profiles';

            if ($photoProfileFile->move($targetPath, $newName)) {
                if($photoProfileName != 'default.jpg'){
                    unlink($targetPath . '/' . $photoProfileName);
                }
                $photoProfileName = $newName; // Gunakan nama baru jika berhasil dipindah
            }
        }

        $updateData = [
            'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
            'description'   => $this->request->getPost('description'),
            'credentials'   => $this->request->getPost('credentials'),
            'linkedin_url'  => $this->request->getPost('linkedin_url'),
            'instagram_url' => $this->request->getPost('instagram_url'),
            'photo_profile' => $photoProfileName
        ];

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
