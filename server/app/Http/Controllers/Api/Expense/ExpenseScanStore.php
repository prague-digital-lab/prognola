<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseScanStore extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $expense = $workspace->expenses()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $request->validate([
            'file' => 'file',
        ]);

        // Create scan
        if (App::isProduction()) {
            $path = '/prognola/production/scans';
        } else {
            $path = '/prognola/dev/scans';
        }

        $file = $request->file('file');

        $saved_path = Storage::disk('do')->putFile($path, $file);

        $scan = new Scan;
        $scan->uuid = Str::uuid();
        $scan->workspace_id = $workspace->id;
        $scan->title = $file->getClientOriginalName();
        $scan->file_path = $saved_path;
        $scan->is_processed = false;
        $scan->expense_id = $expense->id;
        $scan->save();

        return response()->json($scan);
    }
}
