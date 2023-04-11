<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RaceGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'raceId',
        'status',
        'isDeleted'
    ];

    public function raceMembers(): HasMany
    {
        return $this->hasMany(RacePerformance::class, 'raceGroupId', 'id');
    }

}
