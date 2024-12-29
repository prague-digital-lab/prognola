<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Storage;

class ExpenseDestroy extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $expense = $workspace->expenses()
            ->where('uuid', $uuid)
            ->firstOrFail();

        foreach ($expense->scans as $scan) {
            Storage::disk('do')->delete($scan->file_path);
        }

        $expense->scans()->delete();

        $expense->bank_payments()->detach();
        $expense->delete();

        return response()->noContent();
    }
}
