<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * This overrides default Sanctum PersonalAccessToken
 * because we add uuid to token schema.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $fillable = [
        'uuid',
        'name',
        'token',
        'abilities',
        'expires_at',
    ];
}
