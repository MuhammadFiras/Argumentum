<?php

namespace App\Models;

use CodeIgniter\Model;

class AnswerRatingModel extends Model
{
    protected $table            = 'answer_ratings';
    protected $primaryKey       = 'id_rating';
    protected $allowedFields    = ['id_answer', 'id_user', 'rating'];
    protected $useTimestamps    = true;

    public function getRatingByUser(int $id_answer, int $id_user): ?array
    {
        return $this->where('id_answer', $id_answer)
                    ->where('id_user', $id_user)
                    ->first();
    }

    public function getAverageRating(int $id_answer): array
    {
        $ratings = $this->select('rating')->where('id_answer', $id_answer)->findAll();
        $totalRatings = count($ratings);
        $sumRatings = 0;

        if ($totalRatings > 0) {
            foreach ($ratings as $rating) {
                $sumRatings += (int)$rating['rating'];
            }
            return [
                'average' => (float)($sumRatings / $totalRatings),
                'count' => $totalRatings
            ];
        }
        return ['average' => 0.0, 'count' => 0];
    }

    public function saveRating(array $data): bool
    {
        $existingRating = $this->getRatingByUser($data['id_answer'], $data['id_user']);
        if ($existingRating) {
            return $this->update($existingRating['id_rating'], ['rating' => $data['rating']]);
        } else {
            return $this->insert($data);
        }
    }
}