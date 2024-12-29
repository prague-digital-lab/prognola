<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
    ];

    public function expense_categories()
    {
        return $this->hasMany(ExpenseCategory::class)->orderBy('name');
    }
}
