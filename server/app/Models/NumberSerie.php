<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberSerie extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
    ];

    public function getCurrentCode()
    {
        $count = str_pad($this->current_count, $this->digits_count, '0', STR_PAD_LEFT);

        return $this->prefix.date('y').$count.$this->suffix;
    }
}
