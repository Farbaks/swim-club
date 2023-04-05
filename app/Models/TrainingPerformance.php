<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainingId',
        'squadMemberId',
        'time',
        'stroke',
        'rank',
        'points',
        'status',
        'isDeleted'
    ];
}
