<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;


    protected $allowedFields    = ['nama_lengkap', 
                                   'email', 
                                   'password', 
                                   'role', 
                                   'photo_profile',
                                    'photo_profile',
                                    'description',    
                                    'credentials',     
                                    'linkedin_url',     
                                    'instagram_url' 
                                ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}