<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionTopicModel extends Model
{
  protected $table            = 'question_topics';
  protected $primaryKey       = 'question_id';
  protected $returnType       = 'array';
  protected $allowedFields    = ['question_id', 'topic_id'];
}
