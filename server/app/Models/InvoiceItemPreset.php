<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InvoiceItemPreset
 */
class InvoiceItemPreset extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
    ];
}
