<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceStore extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'url_slug' => 'required|string|unique:workspaces,url_slug',
        ]);

        $workspace = new Workspace;
        $workspace->uuid = Str::uuid();
        $workspace->enable_email_inbox = false;
        $workspace->name = $request->name;
        $workspace->url_slug = $request->url_slug;
        $workspace->regenerateInboxEmail();
        $workspace->save();

        $workspace->users()->attach($request->user(), [
            'role' => 'owner',
            'is_active' => true,
        ]);

        return response()->json($workspace);
    }
}
