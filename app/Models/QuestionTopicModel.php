<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionTopicModel extends Model
{
  protected $table            = 'question_topics';
  // TETAPKAN SALAH SATU KOLOM SEBAGAI PRIMARY KEY
  // Ini untuk memenuhi kebutuhan internal CodeIgniter Model.
  // Kita akan tetap menggunakan where() untuk operasi spesifik.
  protected $primaryKey       = 'question_id';
  protected $returnType       = 'array';
  protected $allowedFields    = ['question_id', 'topic_id'];
}
