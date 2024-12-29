<?php

namespace App\Http\Controllers\Api\Workspace\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\User\UserInviteToWorkspace;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserInvite extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $workspace = $this->findAndAuthorizeWorkspace($workspace);
        $user = $this->findOrCreateUser($request->input('email'));

        if ($workspace->users()->find($user->id)) {
            abort(422, 'User is already invited.');
        }

        $user->workspaces()->attach($workspace, [
            'role' => 'invited',
            'is_active' => true,
        ]);

        $user->notify(new UserInviteToWorkspace($request->user()->name, $workspace->name));

        return response()->json($user);
    }

    private function findOrCreateUser($email)
    {
        $user = User::where('email', $email)->first();

        if ($user !== null) {
            return $user;
        }

        $user = new User;
        $user->uuid = Str::uuid();
        $user->email = $email;
        $user->name = $email;
        $user->password = bcrypt(Str::random(35));
        $user->is_registered = false;
        $user->save();

        return $user;
    }
}
