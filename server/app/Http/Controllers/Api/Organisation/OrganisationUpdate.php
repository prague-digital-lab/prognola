<?php

namespace App\Http\Controllers\Api\Organisation;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class OrganisationUpdate extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $organisation = $workspace->organisations()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $organisation->name = $request->input('name', $organisation->name);
        $organisation->internal_note = $request->input('internal_note', $organisation->internal_note);
        $organisation->street = $request->input('street', $organisation->street);
        $organisation->city = $request->input('city', $organisation->city);
        $organisation->country = $request->input('country', $organisation->country);
        $organisation->postal = $request->input('postal', $organisation->postal);
        $organisation->ic = $request->input('ic', $organisation->ic);
        $organisation->dic = $request->input('dic', $organisation->dic);

        $organisation->save();

        return response()->json($organisation);
    }
}
