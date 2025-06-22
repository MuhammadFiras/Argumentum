<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('isLoggedIn') && session()->get('role') !== 'admin') {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Login - Argumentum',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/login', $data);
    }

    public function processLogin()
    {
        // Validasi Input (Server-side)
        $rules = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'Kata sandi wajib diisi.',
                    'min_length' => 'Kata sandi minimal {param} karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email', FILTER_SANITIZE_EMAIL);
        $password = $this->request->getPost('password');

        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $sessionData = [
                'user_id'       => $user['id_user'],
                'nama_lengkap'  => $user['nama_lengkap'],
                'email'         => $user['email'],
                'role'          => $user['role'],
                'photo_profile' => $user['photo_profile'],
                'isLoggedIn'    => TRUE
            ];
            session()->set($sessionData);

            session()->regenerate();

            // Tentukan redirect berdasarkan role
            if ($user['role'] === 'admin') {
                return redirect()->to('/')->with('success', 'Login berhasil! Selamat datang, Admin.');
            } else {
                return redirect()->to('/')->with('success', 'Login berhasil!');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }
    }

    public function register()
    {
        if (session()->get('isLoggedIn') && session()->get('role') !== 'admin') {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Daftar - Argumentum',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/register', $data);
    }

    public function processRegister()
    {
        // Validasi Input (Server-side)
        $rules = [
            'nama_lengkap' => [
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[users.nama_lengkap]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.',
                    'min_length' => 'Nama lengkap minimal {param} karakter.',
                    'max_length' => 'Nama lengkap maksimal {param} karakter.',
                    'is_unique' => 'Nama ini sudah terdaftar.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique' => 'Email ini sudah terdaftar.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'Kata sandi wajib diisi.',
                    'min_length' => 'Kata sandi minimal {param} karakter.'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi kata sandi wajib diisi.',
                    'matches' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.'
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

        $photoProfileName = "default.jpg";
        $photoProfileFile = $this->request->getFile('photo_profile');

        if ($photoProfileFile && $photoProfileFile->isValid() && !$photoProfileFile->hasMoved()) {
            $newName = $photoProfileFile->getRandomName();

            $targetPath = FCPATH . 'assets/images/profiles';

            if ($photoProfileFile->move($targetPath, $newName)) {
                $photoProfileName = $newName;
            }
        }

        $namaLengkap = $this->request->getPost('nama_lengkap');
        $email = $this->request->getPost('email');

        $sanitizedNamaLengkap = strip_tags($namaLengkap);
        $sanitizedEmail = strip_tags($email);
        $sanitizedPhotoProfile = strip_tags($photoProfileName);

        $userData = [
            'nama_lengkap' => $sanitizedNamaLengkap,
            'email' => $sanitizedEmail,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'user',
            'photo_profile' => $sanitizedPhotoProfile
        ];

        if ($this->userModel->insert($userData)) {
            return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan masuk.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal melakukan registrasi. Silakan coba lagi.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}
