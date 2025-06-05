<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users'; // Nama tabel di database
    protected $primaryKey       = 'id_user'; // Primary key tabel
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Bisa 'array' atau 'object' atau entity class
    protected $useSoftDeletes   = false; // Jika true, data tidak benar-benar dihapus tapi ditandai deleted_at

    // Kolom yang diizinkan untuk diisi melalui create/update (mass assignment)
    protected $allowedFields    = ['nama_lengkap', 
                                   'email', 
                                   'password', 
                                   'role', 
                                   'photo_profile',
                                    'photo_profile',
                                    'description',      // <--- BARU
                                    'credentials',      // <--- BARU
                                    'linkedin_url',     // <--- BARU
                                    'instagram_url'     // <--- BARU
                                ];

    // Dates
    protected $useTimestamps = true; // Otomatis mengisi created_at dan updated_at
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Jika useSoftDeletes true

    // Validation (opsional, bisa juga di controller)
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;
    // protected $cleanValidationRules = true;

    // Callbacks (opsional, untuk Aksi sebelum/sesudah insert/update/delete/find)
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];

    /**
     * Mengambil user berdasarkan email
     * @param string $email
     * @return array|object|null
     */
    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}