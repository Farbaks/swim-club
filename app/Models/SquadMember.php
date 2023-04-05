<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SquadMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'squadId',
        'userId',
        'status',
        'isDeleted'
    ];

    public function squad(): HasOne
    {
        return $this->hasOne(Squad::class, 'id', 'squadId');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }
}
