<?php

namespace App\Http\Controllers\Api\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganisationStore extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $organisation = new Organisation;
        $organisation->uuid = Str::uuid();
        $organisation->workspace_id = $workspace->id;

        $organisation->name = $request->input('name');
        $organisation->type = Organisation::ORGANISATION_TYPE_COMPANY;

        $organisation->save();

        return response()->json($organisation);
    }
}
