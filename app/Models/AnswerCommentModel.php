<?php

namespace App\Models;

use CodeIgniter\Model;

class AnswerCommentModel extends Model
{
    protected $table            = 'answer_comments';
    protected $primaryKey       = 'id_comment';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['id_answer', 'id_user', 'comment_text'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getCommentsForAnswer(int $id_answer): array
    {
        $builder = $this->db->table('answer_comments c');
        $builder->select('c.id_comment, c.comment_text, c.created_at, u.id_user, u.nama_lengkap, u.photo_profile');
        $builder->join('users u', 'u.id_user = c.id_user');
        $builder->where('c.id_answer', $id_answer);
        $builder->orderBy('c.created_at', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getCountAllComments()
    {
        return $this->countAllResults();
    }
}
