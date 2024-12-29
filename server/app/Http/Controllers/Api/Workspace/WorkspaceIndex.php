<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkspaceIndex extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $workspaces = $request->user()->workspaces();

        if ($request->has('role')) {
            $workspaces = $workspaces->wherePivot('user_workspace.role', $request->input('role'));
        }

        return response()->json($workspaces->get());
    }
}
