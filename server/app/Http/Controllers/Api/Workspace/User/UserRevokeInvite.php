<?php

namespace App\Http\Controllers\Api\Workspace\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class UserRevokeInvite extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $user = $workspace->users()->where('uuid', $uuid)->first();

        if ($user === null) {
            abort(422, 'Cannot revoke invite of user. User not found in workspace.');
        }

        if ($user->pivot->role !== 'invited') {
            abort(422, 'Cannot revoke invite of user. User invite was already accepted.');
        }

        $workspace->users()->detach($user);

        return response()->json($user);
    }
}
