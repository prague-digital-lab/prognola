<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
 * Public routes.
 */
Route::get('/', function () {
    return response()->json([
        'app_name' => config('app.name'),
        'environment' => config('app.env'),
        'app_url' => config('app.url'),
    ]);
});
