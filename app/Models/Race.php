<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'requirements',
        'startDate',
        'endDate',
        'status',
        'isDeleted'
    ];

    public function raceGroups(): HasMany
    {
        return $this->hasMany(RaceGroup::class, 'raceId', 'id');
    }

}
