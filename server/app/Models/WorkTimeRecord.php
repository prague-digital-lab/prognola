<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WorkTimeRecord
 */
class WorkTimeRecord extends Model
{
    use HasFactory;

    protected $casts = [
        'date_from' => 'datetime:Y-m-d',
        'date_to' => 'datetime:Y-m-d',
    ];

    protected $hidden = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTimeString()
    {
        // TODO:
    }

    public function getLengthInHours()
    {
        return $this->date_from->diffInMinutes($this->date_to) / 60;
    }

    public function countPrice()
    {
        $price = $this->hour_rate * $this->getLengthInHours();
        $this->price = $price;
    }
}
