<?php

namespace App\Http\Controllers\Api\Organisation;

use App\Http\Controllers\Controller;
use Exception;

class OrganisationShow extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $organisation = $workspace->organisations()
            ->with(['expenses', 'incomes'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json($organisation);
    }
}
