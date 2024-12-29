<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class HomepageController extends Controller
{
    /**
     * Feature flags are handled in JS frontend.
     *
     * @return Application|Factory|View
     */
    public function home()
    {
        return view('home.home');
    }
}
