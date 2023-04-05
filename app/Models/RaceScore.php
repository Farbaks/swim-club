<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaceScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'raceGroupMemberId',
        'time',
        'stroke',
        'rank',
        'points',
        'status',
        'isDeleted'
    ];
}
