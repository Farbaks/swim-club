<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RacePerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'club',
        'userId',
        'raceGroupId',
        'time',
        'strokeId',
        'rank',
        'points',
        'status',
        'isDeleted'
    ];
}
