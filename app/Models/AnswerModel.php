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
     * Jawaban terbaik akan diurutkan paling atas.
     * @param int $id_question
     * @return array
     */
    public function getAnswersForQuestion(int $id_question): array
    {
        $builder = $this->db->table('answers a');
        $builder->select('a.*, u.nama_lengkap as user_nama, u.photo_profile as user_photo');
        $builder->join('users u', 'u.id_user = a.id_user');
        $builder->where('a.id_question', $id_question);
        // Urutkan berdasarkan is_best_answer (DESC agar TRUE/1 di atas), lalu berdasarkan tanggal dibuat
        $builder->orderBy('a.is_best_answer', 'DESC');
        $builder->orderBy('a.created_at', 'ASC'); // Atau DESC sesuai preferensi untuk jawaban non-terbaik
        return $builder->get()->getResultArray();
    }

    /**
     * Menandai sebuah jawaban sebagai jawaban terbaik untuk sebuah pertanyaan.
     * Ini akan secara otomatis membatalkan status jawaban terbaik lainnya untuk pertanyaan yang sama.
     * @param int $id_answer ID jawaban yang akan ditandai
     * @param int $id_question ID pertanyaan terkait
     * @return bool True jika berhasil, false jika gagal.
     */
    public function markAsBest(int $id_answer, int $id_question): bool
    {
        // Gunakan transaksi database untuk memastikan integritas data
        $this->db->transStart();

        // 1. Set semua jawaban untuk pertanyaan ini menjadi BUKAN jawaban terbaik
        $this->where('id_question', $id_question)
             ->set(['is_best_answer' => 0]) // Gunakan 0 untuk FALSE
             ->update();

        // 2. Set jawaban yang dipilih menjadi jawaban terbaik
        $this->where('id_answer', $id_answer)
             ->where('id_question', $id_question) // Pastikan jawaban ini milik pertanyaan yang benar
             ->set(['is_best_answer' => 1]) // Gunakan 1 untuk TRUE
             ->update();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    /**
     * Membatalkan status jawaban terbaik untuk sebuah jawaban.
     * (Opsional, jika kamu ingin ada tombol "Unmark Best Answer")
     * @param int $id_answer ID jawaban yang akan di-unmark
     * @return bool
     */
    public function unmarkAsBest(int $id_answer): bool
    {
         return $this->where('id_answer', $id_answer)
                     ->set(['is_best_answer' => 0])
                     ->update();
    }

        /**
     * Mengambil jawaban yang diberikan oleh pengguna tertentu, beserta judul dan slug pertanyaan terkait.
     * @param int $id_user
     * @return array
     */
    public function getAnswersByUserId(int $id_user): array
    {
        $builder = $this->db->table('answers a');
        $builder->select('a.*, q.title as question_title, q.slug as question_slug');
        $builder->join('questions q', 'q.id_question = a.id_question');
        $builder->where('a.id_user', $id_user);
        $builder->orderBy('a.created_at', 'DESC'); // Jawaban terbaru dulu
        return $builder->get()->getResultArray();
    }
}