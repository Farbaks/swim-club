<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaceGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'squadMemberId',
        'raceGroupId',
        'status',
        'isDeleted'
    ];
}
