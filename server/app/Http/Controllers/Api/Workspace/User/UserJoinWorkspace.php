<?php

namespace App\Http\Controllers\Api\Workspace\User;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserJoinWorkspace extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        // User is invited only
        $workspace = Workspace::where('url_slug', $workspace)->firstOrFail();

        if (! Auth::user()->can('viewAsInvited', $workspace)) {
            abort(401, 'Unauthorized action.');
        }

        $user = $workspace->users()
            ->where('uuid', $request->user()->uuid)
            ->firstOrFail();

        if ($user->pivot->role !== 'invited') {
            abort(422, 'User cannot join this workspace. Invalid role. Role [invited] required.');
        }

        $workspace->users()->updateExistingPivot($user->id, [
            'role' => 'member',
        ]);

        return response()->json($workspace);
    }
}
