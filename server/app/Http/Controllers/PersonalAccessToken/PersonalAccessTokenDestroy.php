<?php

namespace App\Http\Controllers\PersonalAccessToken;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonalAccessTokenDestroy extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($uuid, Request $request)
    {
        $request->user()
            ->tokens()
            ->where('uuid', $uuid)
            ->delete();

        return response(null, 204);
    }
}
