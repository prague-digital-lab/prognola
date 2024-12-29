<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class Register extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        $password = Hash::make($validated['password']);

        $invited_user = User::where('email', $request->email)
            ->where('is_registered', false)
            ->first();

        if ($invited_user !== null) {
            $user = $invited_user;
            $user->password = $password;
            $user->is_registered = true;
            $user->save();

        } else {

            $request->validate([
                'email' => 'unique:users',
            ]);

            $user = new User;
            $user->uuid = Str::uuid();
            $user->name = $validated['email'];
            $user->email = $validated['email'];
            $user->password = $password;
            $user->save();
        }

        event(new Registered($user));

        if (! app()->environment('testing')) {

            //            $discord_channel_id = app()->hasDebugModeEnabled() ? '1225090953564520459' : '1225085797510811659';
            //            Notification::route('discord', $discord_channel_id)
            //                ->notify(new DiscordUserRegistered($user));

            Log::info("User $user->email registered.");
        }

        return $user;
    }
}
