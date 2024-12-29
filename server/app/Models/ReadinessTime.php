<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;

/**
 * App\Models\ReadinessTime
 */
class ReadinessTime extends Model
{
    use HasFactory;
    use Notifiable;

    protected $casts = [
        'date_from' => 'datetime:Y-m-d',
        'date_to' => 'datetime:Y-m-d',
    ];

    protected $hidden = [
        'id',
    ];

    public function routeNotificationForDiscord()
    {
        if (App::environment() == 'production') {
            return '1044920409142861876';

        } else {
            return '1111963282018947092';
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDateTimeString()
    {
        return $this->date_from->minDayName.' '.$this->date_from->format('j.n.').' ('.$this->date_from->format('G:i').' - '.$this->date_to->format('G:i').')';
    }

    public function getDateString()
    {
        return $this->date_from->minDayName.' '.$this->date_from->format('j.n.');
    }

    public function getTimeString()
    {
        return $this->date_from->format('G:i').' - '.$this->date_to->format('G:i');
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class)->orderBy('date_from');
    }

    public function getLengthInHours()
    {
        return $this->date_from->diffInMinutes($this->date_to) / 60;
    }
}
