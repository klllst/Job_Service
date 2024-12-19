<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ad extends Model
{
    protected $fillable = [
        'name',
        'description',
        'cost',
        'status',
        'employer_id',
        'worker_id',
    ];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
