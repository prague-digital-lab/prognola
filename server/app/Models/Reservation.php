<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * App\Models\Reservation
 */
class Reservation extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    const CUSTOMER_TYPE_FAMILY = 'family';

    const CUSTOMER_TYPE_SCHOOL = 'school';

    const CUSTOMER_TYPE_BIRTHDAY = 'birthday';

    const CUSTOMER_TYPE_WEDDING_PARTY = 'wedding_party';

    const CUSTOMER_TYPE_COMPANY_TEAMBUILDING = 'company_teambuilding';

    const CUSTOMER_TYPE_NONPROFIT = 'nonprofit_organisation';

    const CUSTOMER_TYPE_FREE_TIME_ORGANISATION = 'free_time_organisation';

    const CUSTOMER_TYPE_SPORT_ORGANISATION = 'sport_organisation';

    const STATUS_PLANNED = 'planned';

    const STATUS_PLAYING = 'playing';

    const STATUS_FINISHED = 'finished';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_NOT_ARRIVED = 'not_arrived';

    protected $casts = [
        'date_from' => 'datetime:Y-m-d',
        'date_to' => 'datetime:Y-m-d',
    ];

    protected $fillable
        = [
            'name',
            'date_from',
            'team_count',
            'email',
            'phone',
            'description',
            'internal_note',
        ];

    protected $hidden = [
        'id',
    ];

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function assigned_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function affiliate_code()
    {
        return $this->belongsTo(AffiliateCode::class);
    }

    public function affiliate_partners()
    {
        return $this->belongsToMany(AffiliatePartner::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getDateString()
    {
        return $this->date_from->minDayName.' '.$this->date_from->format('j.n. G:i');
    }

    public function getDateStringWithYear()
    {
        return $this->date_from->minDayName.' '.$this->date_from->format('j.n.Y G:i');
    }

    public function getTimeString()
    {
        return $this->date_from->format('G:i').' - '.$this->date_to->format('G:i');
    }

    public function routeNotificationForDiscord()
    {
        if (App::environment() == 'production') {
            return '1014596467589128275';

        } else {
            return '1111963282018947092';
        }
    }

    public function getCustomerTypeString()
    {
        if ($this->customer_type == self::CUSTOMER_TYPE_FAMILY) {
            return 'rodiny a jednotlivci';
        } elseif ($this->customer_type == self::CUSTOMER_TYPE_SCHOOL) {
            return 'školní třída';
        } elseif ($this->customer_type == self::CUSTOMER_TYPE_BIRTHDAY) {
            return 'oslava narozenin (nebo narozeninová sleva)';
        } elseif ($this->customer_type == self::CUSTOMER_TYPE_WEDDING_PARTY) {
            return 'rozlučka se svobodou';
        } elseif ($this->customer_type == self::CUSTOMER_TYPE_COMPANY_TEAMBUILDING) {
            return 'firmy a organizace (dospělí)';
        } elseif ($this->customer_type == self::CUSTOMER_TYPE_NONPROFIT) {
            return 'nezisková organizace (dospělí)';
        } elseif ($this->customer_type == self::CUSTOMER_TYPE_FREE_TIME_ORGANISATION) {
            return 'mimoškolní dětské skupiny (tábory, SVČ, volnočasovky, sport)';
        } elseif ($this->customer_type == self::CUSTOMER_TYPE_SPORT_ORGANISATION) {
            return 'sportovní organizace';
        } else {
            return 'neznámý';
        }
    }

    public function readiness_times()
    {
        return $this->belongsToMany(ReadinessTime::class)->orderBy('date_from');
    }

    public function getAssignedUsers()
    {
        $users = new Collection;

        foreach ($this->readiness_times as $time) {
            if ($time->user && $users->doesntContain($time->user)) {
                $users->add($time->user);
            }
        }

        return $users;
    }

    public function getTeamCountString(): string
    {
        $count = $this->team_count;

        if ($count == 0) {
            return $count.' hráčů';
        } elseif ($count == 1) {
            return $count.' hráč';
        } elseif ($count < 5) {
            return $count.' hráči';
        } else {
            return $count.' hráčů';
        }
    }

    public function getTitle()
    {
        return 'Rezervace '.$this->id.' - '.$this->customer->getName();
    }
}
