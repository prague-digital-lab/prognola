<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ExportExpensesScansUrl extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $from = $request->input('from');
        $to = $request->input('to');

        $route = URL::temporarySignedRoute(
            'scans.download', now()->addMinutes(5), ['workspace' => $workspace->url_slug, 'from' => $from, 'to' => $to]
        );

        return response()->json([
            'export_url' => $route,
        ]);
    }
}
