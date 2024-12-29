<?php

namespace App\Http\Controllers\Api\Scan;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScanStore extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $request->validate([
            'title' => 'required',
            'file' => 'required|file',
            'expense' => 'required',
        ]);

        // Upload file
        $file = $request->file('file');

        if (App::isProduction()) {
            $path = '/prognola/production/scans';
        } else {
            $path = '/prognola/dev/scans';
        }

        $saved_path = Storage::disk('do')->putFile($path, $file);

        // Save scan to database
        $scan = new Scan;

        $scan->workspace_id = $workspace->id;
        $scan->uuid = Str::uuid();

        // Related expense
        $expense = $workspace->expenses()
            ->where('uuid', $request->input('expense'))
            ->firstOrFail();
        $scan->expense_id = $expense->id;

        $scan->file_path = $saved_path;
        $scan->title = $request->input('title', $file->getClientOriginalName());
        $scan->save();

        return response()->json($scan);
    }
}
