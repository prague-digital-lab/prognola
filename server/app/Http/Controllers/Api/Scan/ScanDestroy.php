<?php

namespace App\Http\Controllers\Api\Scan;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Storage;

class ScanDestroy extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke($workspace, $uuid)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $scan = $workspace->scans()
            ->where('uuid', $uuid)
            ->firstOrFail();

        Storage::disk('do')->delete($scan->file_path);

        $scan->delete();

        return response()->noContent();
    }
}
