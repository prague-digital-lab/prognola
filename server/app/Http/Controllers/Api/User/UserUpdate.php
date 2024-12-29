<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserUpdate extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $user->name = $request->input('name', $user->name);
        $user->save();
    }
}
