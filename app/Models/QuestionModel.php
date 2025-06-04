<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table            = 'questions';
    protected $primaryKey       = 'id_question';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['id_user', 'title', 'content', 'slug'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil pertanyaan dengan informasi user yang bertanya.
     * @param string|null $slug Jika null, ambil semua pertanyaan. Jika ada, ambil pertanyaan spesifik.
     * @return array|object|null
     */
    public function getQuestionsWithUser(string $slug = null)
    {
        $builder = $this->db->table('questions q');
        $builder->select('q.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo');
        $builder->join('users u', 'u.id_user = q.id_user');
        $builder->orderBy('q.created_at', 'DESC');

        if ($slug === null) {
            return $builder->get()->getResultArray(); // Ambil semua pertanyaan
        }
        return $builder->where('q.slug', $slug)->get()->getRowArray(); // Ambil satu pertanyaan berdasarkan slug
    }

     /**
     * Mengambil pertanyaan berdasarkan ID user.
     * @param int $userId
     * @return array
     */
    public function getQuestionsByUserId(int $userId): array
    {
        $builder = $this->db->table('questions q');
        $builder->select('q.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo');
        $builder->join('users u', 'u.id_user = q.id_user');
        $builder->where('q.id_user', $userId);
        $builder->orderBy('q.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }
}