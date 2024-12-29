<?php

namespace App\Http\Controllers\Api\Organisation;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class OrganisationIndex extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);
        $organisations = $workspace->organisations;

        return response()->json($organisations);
    }
}
