<?php

namespace App\Http\Controllers\PersonalAccessToken;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonalAccessTokenStore extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $token = $request->user()->createToken($request->input('name'));

        $token->accessToken->type = 'user_api_token';
        $token->accessToken->save();

        return response()->json(['token' => $token->plainTextToken]);
    }
}
