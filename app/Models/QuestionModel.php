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

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // public function getQuestionsWithUser(string $slug = null)
    // {
    //     $builder = $this->db->table('questions q');
    //     $builder->select('q.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo');
    //     $builder->join('users u', 'u.id_user = q.id_user');
    //     $builder->orderBy('q.created_at', 'DESC');

    //     if ($slug === null) {
    //         return $builder->get()->getResultArray();
    //     }
    //     return $builder->where('q.slug', $slug)->get()->getRowArray();
    // }

    public function getAllQuestionsWithDetails(string $topicId = null): array
    {
        $builder = $this->db->table('questions q');
        // Tambahkan GROUP_CONCAT untuk mengambil semua nama topik dalam satu string
        $builder->select('q.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo, 
                       GROUP_CONCAT(t.name SEPARATOR ", ") as topics');

        $builder->join('users u', 'u.id_user = q.id_user');
        // Gunakan LEFT JOIN agar pertanyaan yang tidak punya topik (seharusnya tidak terjadi) tetap muncul
        $builder->join('question_topics qt', 'qt.question_id = q.id_question', 'left');
        $builder->join('topics t', 't.id = qt.topic_id', 'left');

        // === BAGIAN BARU UNTUK FILTER ===
        if ($topicId !== null) {
            // Ini adalah subquery untuk memastikan kita hanya mengambil pertanyaan
            // yang memiliki topic_id yang kita mau.
            $subQuery = $this->db->table('question_topics')->select('question_id')->where('topic_id', $topicId);
            $builder->whereIn('q.id_question', $subQuery);
        }
        // =================================

        // GROUP BY id pertanyaan adalah kunci agar GROUP_CONCAT berfungsi dengan benar
        $builder->groupBy('q.id_question');
        $builder->orderBy('q.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    // public function getQuestionsByUserId(int $userId): array
    // {
    //     $builder = $this->db->table('questions q');
    //     $builder->select('q.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo');
    //     $builder->join('users u', 'u.id_user = q.id_user');
    //     $builder->where('q.id_user', $userId);
    //     $builder->orderBy('q.created_at', 'DESC');
    //     return $builder->get()->getResultArray();
    // }

    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    public function getQuestionsByUserId(int $userId): array
    {
        $builder = $this->db->table('questions q');
        $builder->select('q.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo, 
                       GROUP_CONCAT(t.name SEPARATOR ", ") as topics');
        $builder->join('users u', 'u.id_user = q.id_user');
        $builder->join('question_topics qt', 'qt.question_id = q.id_question', 'left');
        $builder->join('topics t', 't.id = qt.topic_id', 'left');
        $builder->where('q.id_user', $userId); // Filter berdasarkan ID user
        $builder->groupBy('q.id_question');
        $builder->orderBy('q.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getQuestionWithDetails(string $slug): ?array
    {
        $builder = $this->db->table('questions q');
        $builder->select('q.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo, 
                       GROUP_CONCAT(t.name SEPARATOR ", ") as topics');
        $builder->join('users u', 'u.id_user = q.id_user');
        $builder->join('question_topics qt', 'qt.question_id = q.id_question', 'left');
        $builder->join('topics t', 't.id = qt.topic_id', 'left');
        $builder->where('q.slug', $slug);
        $builder->groupBy('q.id_question'); // Penting untuk GROUP_CONCAT

        $query = $builder->get();
        return $query->getRowArray();
    }
}
