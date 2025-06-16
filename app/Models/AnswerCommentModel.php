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
    
    // Tentukan kolom mana yang boleh diisi melalui method insert() atau update()
    protected $allowedFields    = ['id_answer', 'id_user', 'comment_text'];

    // Mengaktifkan penggunaan kolom created_at dan updated_at secara otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil semua komentar untuk sebuah jawaban (id_answer).
     * Data yang diambil juga termasuk informasi pengguna yang berkomentar (nama & foto profil).
     *
     * @param int $id_answer
     * @return array
     */
    public function getCommentsForAnswer(int $id_answer): array
    {
        // Kita menggunakan Query Builder untuk membuat query yang lebih kompleks
        $builder = $this->db->table('answer_comments c'); // 'c' adalah alias untuk answer_comments
        
        // Memilih kolom yang kita butuhkan
        $builder->select('c.id_comment, c.comment_text, c.created_at, u.id_user, u.nama_lengkap, u.photo_profile');
        
        // Menggabungkan (JOIN) dengan tabel 'users' (alias 'u')
        $builder->join('users u', 'u.id_user = c.id_user');
        
        // Filter berdasarkan ID jawaban
        $builder->where('c.id_answer', $id_answer);
        
        // Urutkan berdasarkan komentar terbaru
        $builder->orderBy('c.created_at', 'ASC'); // ASC agar komentar lama di atas, atau DESC untuk sebaliknya
        
        // Eksekusi query dan kembalikan hasilnya sebagai array
        return $builder->get()->getResultArray();
    }
}