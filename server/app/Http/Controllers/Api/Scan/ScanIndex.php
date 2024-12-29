<?php

namespace App\Http\Controllers\Api\Scan;

use App\Http\Controllers\Controller;

class ScanIndex extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke($workspace, $uuid)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        return response()->json($workspace->scans);
    }
}
