<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Login extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $loginUserData['email'])->first();

        if (! $user || ! Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials',
            ], 401);
        }

        $token = $user->createToken($user->name.'-AuthToken');

        return response()->json([
            'token' => $token->plainTextToken,
        ]);
    }
}
