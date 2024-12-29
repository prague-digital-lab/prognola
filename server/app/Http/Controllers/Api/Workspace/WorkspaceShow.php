<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class WorkspaceShow extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        if ($workspace->inbox_email_hash === null) {
            $workspace->regenerateInboxEmail();
            $workspace->save();
        }

        $workspace = $workspace->append(['inbox_email']);

        return response()->json($workspace);
    }
}
