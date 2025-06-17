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

    public function getAllQuestionsWithDetails(string $topicId = null): array
    {
        $builder = $this->db->table('questions q');
        $builder->select('q.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo, 
                       GROUP_CONCAT(t.name SEPARATOR ", ") as topics');

        $builder->join('users u', 'u.id_user = q.id_user');
        $builder->join('question_topics qt', 'qt.question_id = q.id_question', 'left');
        $builder->join('topics t', 't.id = qt.topic_id', 'left');

        if ($topicId !== null) {
            $subQuery = $this->db->table('question_topics')->select('question_id')->where('topic_id', $topicId);
            $builder->whereIn('q.id_question', $subQuery);
        }

        $builder->groupBy('q.id_question');
        $builder->orderBy('q.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

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
        $builder->where('q.id_user', $userId);
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
        $builder->groupBy('q.id_question');

        $query = $builder->get();
        return $query->getRowArray();
    }
}
