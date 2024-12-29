<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class WorkspaceUpdateEmailSettings extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        if ($request->has('enable_email_inbox')) {
            $workspace->enable_email_inbox = $request->input('enable_email_inbox');

            if ($workspace->inbox_email_hash === null) {
                $workspace->regenerateInboxEmail();
            }
        }

        $workspace->save();

        return response()->json($workspace);
    }
}
