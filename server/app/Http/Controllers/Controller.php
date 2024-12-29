<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getDateFrom(Request $request)
    {
        $date_from = $request->input('date_from');

        if ($date_from) {
            $date_from = Carbon::parse($date_from);
        } else {
            $date_from = Carbon::now()->startOfWeek();
        }

        return $date_from;
    }

    protected function getDateTo(Request $request)
    {
        $date_to = $request->input('date_to');

        if ($date_to) {
            $date_to = Carbon::parse($date_to)->endOfDay();
        } else {
            $date_to = Carbon::now()->endOfWeek();
        }

        return $date_to;
    }

    protected function getDateProperty(Request $request)
    {
        $date_property_name = $request->input('date_property');

        if (! $date_property_name) {
            $date_property_name = 'created_at';
        }

        return $date_property_name;
    }

    /**
     * @throws Exception
     */
    protected function findAndAuthorizeWorkspace($workspace)
    {
        $workspace = Workspace::where('url_slug', $workspace)->firstOrFail();

        if (Auth::user()->can('view', $workspace)) {
            return $workspace;
        }

        abort(401, 'Unauthorized action.');
    }
}
