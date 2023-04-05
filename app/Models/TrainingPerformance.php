<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrainingPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainingId',
        'squadMemberId',
        'time',
        'strokeId',
        'rank',
        'points',
        'trainingDate',
        'status',
        'isDeleted'
    ];

    public function training(): HasOne
    {
        return $this->hasOne(Training::class, 'id', 'trainingId');
    }

    public function squadMember(): HasOne
    {
        return $this->hasOne(SquadMember::class, 'id', 'squadMemberId');
    }

    public function stroke(): HasOne
    {
        return $this->hasOne(Stroke::class, 'id', 'strokeId');
    }
}
