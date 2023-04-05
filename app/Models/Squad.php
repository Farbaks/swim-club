<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Squad extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rank',
        'coachId',
        'status',
        'isDeleted'
    ];


    public function coach(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'coachId');
    }

}
