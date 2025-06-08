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

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAnswersForQuestion(int $id_question): array
    {
        $builder = $this->db->table('answers a');
        $builder->select('a.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo');
        $builder->join('users u', 'u.id_user = a.id_user');
        $builder->where('a.id_question', $id_question);
        $builder->orderBy('a.is_best_answer', 'DESC');
        $builder->orderBy('a.created_at', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function markAsBest(int $id_answer, int $id_question): bool
    {
        $this->db->transStart();

        $this->where('id_question', $id_question)
             ->set(['is_best_answer' => 0])
             ->update();

        $this->where('id_answer', $id_answer)
             ->where('id_question', $id_question)
             ->set(['is_best_answer' => 1])
             ->update();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function unmarkAsBest(int $id_answer): bool
    {
         return $this->where('id_answer', $id_answer)
                     ->set(['is_best_answer' => 0])
                     ->update();
    }

    public function getAnswersByUserId(int $id_user): array
    {
        $builder = $this->db->table('answers a');
        $builder->select('a.*, q.title as question_title, q.slug as question_slug');
        $builder->join('questions q', 'q.id_question = a.id_question');
        $builder->where('a.id_user', $id_user);
        $builder->orderBy('a.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }
}