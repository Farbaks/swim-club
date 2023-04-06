<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Relationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardianId',
        'wardId',
        'status',
        'isDeleted'
    ];

    public function ward(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'wardId');
    }

    public function guardian(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'guardianId');
    }
}
