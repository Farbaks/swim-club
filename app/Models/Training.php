<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'requirements',
        'startTime',
        'endTime',
        'day',
        'interval',
        'squadId',
        'status',
        'isDeleted'
    ];
}
