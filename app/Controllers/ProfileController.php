<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\QuestionModel;
use App\Models\AnswerModel;
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

    public function view($id_user = null)
    {
        $targetUserId = $id_user;
        $isOwnProfile = false;

        if ($targetUserId === null) {
            if (!session()->get('isLoggedIn')) {
                return redirect()->to('/login')->with('error', 'Anda harus login untuk melihat profil Anda.');
            }
            $targetUserId = session()->get('user_id');
            $isOwnProfile = true;
        }

        $user = $this->userModel->find($targetUserId);

        if (!$user) {
            return redirect()->to('/')->with('error', 'Profil pengguna tidak ditemukan.');
        }

        $questions = $this->questionModel->getQuestionsByUserId($targetUserId);
        $answersByUser = $this->answerModel->getAnswersByUserId($targetUserId);

        $data = [
            'title' => 'Profil ' . esc($user['nama_lengkap']),
            'user_profile' => $user,
            'questions_by_user' => $questions,
            'answers_by_user' => $answersByUser,
            'is_own_profile' => $isOwnProfile
        ];

        return view('profile/view_profile', $data);
    }

    public function edit()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Anda harus login untuk mengedit profil.');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Data pengguna tidak ditemukan. Silakan login kembali.');
        }

        $data = [
            'title' => 'Edit Profil Saya',
            'user_data' => $user,
            'validation' => \Config\Services::validation()
        ];
        return view('profile/edit_profile', $data);
    }

    public function update()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if ($user['nama_lengkap'] == $this->request->getVar('nama_lengkap')) {
            $ruleNamaLengkap = 'required|min_length[3]|max_length[100]';
        } else {
            $ruleNamaLengkap = 'required|min_length[3]|max_length[100]|is_unique[users.nama_lengkap]';
        }

        $rules = [
            'nama_lengkap' => [
                'rules' => $ruleNamaLengkap,
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.',
                    'min_length' => 'Nama lengkap minimal {param} karakter.',
                    'max_length' => 'Nama lengkap maksimal {param} karakter.',
                    'is_unique' => 'Nama lengkap sudah ada.'
                ]
            ],
            'description' => [
                'rules' => 'max_length[500]',
                'errors' => [
                    'max_length' => 'Deksripsi tidak boleh lebih dari 500 kata',
                ]
            ],
            'credentials' => [
                'rules' => 'max_length[500]',
                'errors' => [
                    'max_length' => 'Kredensial tidak boleh lebih dari 500 kata',
                ]
            ],
            'linkedin_url' => [
                'rules' => 'permit_empty|max_length[255]|valid_url_strict|is_linkedin_url', 
                'errors' => [
                    'max_length' => "Karakter dalam Link melebihi 255 karakter",
                    'valid_url_strict' => "Pastikan diawali dengan http:// atau https:// ",
                    'is_linkedin_url' => 'Link bukan dari LinkedIn.com ', 
                ]
            ],
            'instagram_url' => [
                'rules' => 'permit_empty|max_length[255]|is_instagram_url', 
                'errors' => [
                    'max_length' => "Karakter dalam Link melebihi 255 karakter",
                    'valid_url_strict' => "Pastikan diawali dengan http:// atau https:// ",
                    'is_instagram_url' => 'Link bukan dari Instagram', 
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

        $photoProfileName = $user['photo_profile'];
        $photoProfileFile = $this->request->getFile('photo_profile');

        if ($photoProfileFile && $photoProfileFile->isValid() && !$photoProfileFile->hasMoved()) {
            $newName = $photoProfileFile->getRandomName();

            $targetPath = FCPATH . 'assets/images/profiles';

            if ($photoProfileFile->move($targetPath, $newName)) {
                if ($photoProfileName != 'default.jpg' && file_exists($targetPath . '/' . $photoProfileName)) {
                    unlink($targetPath . '/' . $photoProfileName);
                }
                $photoProfileName = $newName;
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
            if (session()->get('nama_lengkap') != $updateData['nama_lengkap']) {
                session()->set('nama_lengkap', $updateData['nama_lengkap']);
            }
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
        } else {
            return redirect()->to('/profile/edit')->withInput()->with('error', 'Gagal memperbarui profil.');
        }
    }

    public function delete($userId)
    {
        if ($this->userModel->delete($userId)) {
            return redirect()->to('/admin/tables/users')->with('success', 'Data User berhasil dihapus.');
        } else {
            return redirect()->to('/admin/tables/users')->with('error', 'Gagal menghapus data User.');
        }
    }
}
