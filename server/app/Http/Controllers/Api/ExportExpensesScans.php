<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Facades\Zip;

class ExportExpensesScans extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke($workspace, Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $workspace = Workspace::where('url_slug', $workspace)
            ->firstOrFail();

        $from = Carbon::parse($request->input('from'))->startOfDay();
        $to = Carbon::parse($request->input('to'))->endOfDay();

        $expenses = $workspace->expenses()
            ->with('scans')
            ->whereBetween('received_at', [$from, $to])
            ->get();

        $scans = collect();

        foreach ($expenses as $expense) {
            foreach ($expense->scans as $scan) {
                $scans->add($scan);
            }
        }

        $name = $workspace->name.' - export výdajů.zip';
        $zip = Zip::create($name);

        /**
         * @var Scan $scan
         */
        foreach ($scans as $scan) {
            //            $name = '/' . 'Sken č. ' . $scan->id;
            $zip->addFromDisk(Storage::disk('do'), $scan->file_path, null);
        }

        return $zip;
    }
}
