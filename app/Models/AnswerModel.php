<?php

namespace App\Models;

use CodeIgniter\Model;

class AnswerModel extends Model
{
    protected $table            = 'answers';
    protected $primaryKey       = 'id_answer';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['id_question', 'id_user', 'content', 'is_best_answer'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil jawaban untuk pertanyaan tertentu, beserta informasi user yang menjawab.
     * @param int $id_question
     * @return array
     */
    public function getAnswersForQuestion(int $id_question): array
    {
        $builder = $this->db->table('answers a');
        $builder->select('a.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo');
        $builder->join('users u', 'u.id_user = a.id_user');
        $builder->where('a.id_question', $id_question);
        $builder->orderBy('a.created_at', 'ASC'); // Atau DESC berdasarkan preferensi
        return $builder->get()->getResultArray();
    }
}