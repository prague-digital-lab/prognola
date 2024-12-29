<?php

namespace App\Http\Controllers\PersonalAccessToken;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonalAccessTokenIndex extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $tokens = $request->user()
            ->tokens()
            ->where('type', 'user_api_token')
            ->get();

        return response()->json($tokens);
    }
}
