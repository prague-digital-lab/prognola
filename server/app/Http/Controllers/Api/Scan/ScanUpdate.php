<?php

namespace App\Http\Controllers\Api\Scan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScanUpdate extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $scan = $workspace->scans()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $request->validate([
            'title' => 'nullable|string',
            'expense' => 'nullable|uuid',
        ]);

        $scan->title = $request->input('title', $scan->title);

        // Update expense
        if ($request->has('expense')) {
            $expense = $workspace->expenses()
                ->where('uuid', $request->input('expense'))
                ->firstOrFail();

            $scan->expense_id = $expense->id;
        }

        $scan->save();

        return response()->json($scan);
    }
}
