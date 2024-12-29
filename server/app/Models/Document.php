<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Document extends Model
{
    use HasFactory;

    protected $casts = [
        'issued_at' => 'date',
    ];

    protected $hidden = [
        'id',
    ];

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'assignable');
    }
}
