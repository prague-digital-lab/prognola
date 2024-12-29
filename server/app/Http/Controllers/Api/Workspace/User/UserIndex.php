<?php

namespace App\Http\Controllers\Api\Workspace\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class UserIndex extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        return response()->json($workspace->users);
    }
}
