<?php

namespace App\Http\Controllers\Api\Scan;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Illuminate\Http\Request;

class ScanShow extends Controller
{
    public function __invoke($id, Request $request)
    {
        $scan = Scan::findOrFail($id);
        $scan->load(['received_invoice']);
        $scan->append('file_url');

        return response()->json($scan);
    }
}
