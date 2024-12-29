<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        return view('admin.campaign.index', [
            'campaigns' => Campaign::all(),
        ]);
    }

    public function create()
    {
        return view('admin.campaign.create');
    }

    public function store(Request $request)
    {
        $campaign = new Campaign;
        $campaign->name = $request->input('name');
        $campaign->save();

        return redirect()->route('admin.campaigns.index');
    }
}
