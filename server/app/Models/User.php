<?php

namespace App\Models;

use App\Notifications\EmailVerificationNotification;
use DateTimeInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

/**
 * App\Models\User
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public const USER_ROLE_RECEPTION = 'recepce';

    public const USER_ROLE_ADMIN = 'admin';

    public const USER_ROLE_ACCOUNTANT = 'accountant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable
        = [
            'name',
            'email',
            'password',
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden
        = [
            'id',
            'password',
            'remember_token',
        ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts
        = [
            'logged_at' => 'datetime:Y-m-d',
            'email_verified_at' => 'datetime',
            'birth_date' => 'datetime',
        ];

    public function getProfilePhotoUrlAttribute()
    {
        return config('app.url').'/storage/profile_photos/'.$this->profile_photo_path;
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class)
            ->withPivot(['role', 'is_active']);
    }

    public function work_time_records(): HasMany
    {
        return $this->hasMany(WorkTimeRecord::class);
    }

    public function getPendingWorkTimeRecord()
    {
        return $this->work_time_records->where('date_to', null)->first();
    }

    public function documents(): MorphToMany
    {
        return $this->morphedByMany(Document::class, 'assignable');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'related_user_id');
    }

    public function getDiscordIdSignatureOrName(): string
    {
        if ($this->discord_id) {
            return "<@$this->discord_id>";
        } else {
            return $this->name;
        }
    }

    public function getRoleString(): string
    {
        if ($this->role == self::USER_ROLE_RECEPTION) {
            return 'recepční';
        } elseif ($this->role == self::USER_ROLE_ADMIN) {
            return 'admin';
        } elseif ($this->role == self::USER_ROLE_ACCOUNTANT) {
            return 'účetní';
        } else {
            return 'neznámý typ uživatele';
        }
    }

    public function isRole($role): bool
    {
        return $this->role == $role;
    }

    public function isReception(): bool
    {
        return $this->isRole(self::USER_ROLE_RECEPTION) || $this->isRole(self::USER_ROLE_ADMIN);
    }

    public function isAccountant(): bool
    {
        return $this->isRole(self::USER_ROLE_ACCOUNTANT) || $this->isRole(self::USER_ROLE_ADMIN);
    }

    /**
     * We override the default notification and will use our own
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new EmailVerificationNotification);
    }

    public function createToken(string $name, array $abilities = ['*'], ?DateTimeInterface $expiresAt = null): NewAccessToken
    {
        $plainTextToken = $this->generateTokenString();

        $token = $this->tokens()->create([
            'uuid' => Str::uuid(),
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }
}
