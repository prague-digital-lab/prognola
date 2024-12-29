<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class WorkspaceUpdateDiscordSettings extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        if ($request->has('discord_channel_id')) {
            $workspace->discord_channel_id = $request->input('discord_channel_id');
        } else {
            $workspace->discord_channel_id = null;
        }

        $workspace->save();

        return response()->json($workspace);
    }
}
