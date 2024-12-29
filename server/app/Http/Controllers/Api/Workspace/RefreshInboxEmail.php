<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RefreshInboxEmail extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $workspace->regenerateInboxEmail();
        $workspace->save();

        return response()->json(['inbox_email' => $workspace->getInboxEmailAttribute()]);
    }
}
