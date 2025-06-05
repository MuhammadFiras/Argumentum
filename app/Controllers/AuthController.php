<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $helpers = ['form', 'url']; // Memuat helper form dan URL

    public function __construct()
    {
        $this->userModel = new UserModel();
        // Memuat service session jika belum otomatis (biasanya sudah)
        // $this->session = \Config\Services::session();
    }

    // === HALAMAN LOGIN ===
    public function login()
    {
        // Cek jika sudah login, redirect ke halaman home
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/'); // Redirect ke base_url (nanti akan diatur ke home)
        }
        $data['title'] = 'Login';
        return view('auth/login', $data);
    }

    public function processLogin()
    {
        // Validasi Input (Server-side)
        $rules = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Surel wajib diisi.',
                    'valid_email' => 'Format surel tidak valid.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Kata sandi wajib diisi.',
                    'min_length' => 'Kata sandi minimal {param} karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil data dari POST request
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        // $rememberMe = $this->request->getPost('remember_me'); // Implementasi 'remember me' bisa ditambahkan nanti

        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Password cocok, buat session
            $sessionData = [
                'user_id'       => $user['id_user'],
                'nama_lengkap'  => $user['nama_lengkap'],
                'email'         => $user['email'],
                'role'          => $user['role'],
                'photo_profile' => $user['photo_profile'],
                'isLoggedIn'    => TRUE
            ];
            session()->set($sessionData);

            // Regenerasi ID session untuk keamanan
            session()->regenerate();

            // Tentukan redirect berdasarkan role
            if ($user['role'] === 'admin') {
                // Arahkan ke dashboard admin jika ada
                // return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang, Admin.');
                return redirect()->to('/')->with('success', 'Login berhasil! Selamat datang, Admin.'); // Sementara ke home
            } else {
                return redirect()->to('/')->with('success', 'Login berhasil!');
            }
        } else {
            // Surel tidak ditemukan atau password salah
            return redirect()->back()->withInput()->with('error', 'Surel atau kata sandi salah.');
        }
    }

    // === HALAMAN REGISTER ===
    public function register()
    {
         // Cek jika sudah login, redirect ke halaman home
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        $data['title'] = 'Register';
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
                    'required' => 'Surel wajib diisi.',
                    'valid_email' => 'Format surel tidak valid.',
                    'is_unique' => 'Surel ini sudah terdaftar.'
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
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Data valid, simpan ke database
        $userData = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT), // HASH PASSWORDNYA!
            'role' => 'user' // Default role
            // 'photo_profile' akan menggunakan default dari DB
        ];

        if ($this->userModel->insert($userData)) {
             return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan masuk.');
        } else {
            // Seharusnya tidak terjadi jika validasi `is_unique` berfungsi dan tidak ada error DB
            return redirect()->back()->withInput()->with('error', 'Gagal melakukan registrasi. Silakan coba lagi.');
        }
    }

    // === LOGOUT ===
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}