<?php

namespace App\Http\Controllers\Api\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservationApiController extends Controller
{
    public function today()
    {
        $reservations = Reservation::where('date_from', '>=', Carbon::now()->startOfDay())
            ->where('date_from', '<=', Carbon::now()->endOfDay())
            ->get();

        return new Response($reservations);
    }

    public function store(Request $request): Response
    {
        $team = new Team;
        $team->game_internal_id = $request['game_internal_id'];
        $team->reservation_id = $request['reservation_id'];
        $team->player_count = $request['player_count'];
        $team->name = $request['name'];
        $team->game_time = $request['game_time'];
        $team->game_time_rough = $request['game_time_rough'];
        $team->save();

        return new Response($team);
    }

    public function update($id, Request $request): Response
    {
        $team = Team::findOrFail($id);

        $team->game_internal_id = $request['game_internal_id'];
        $team->reservation_id = $request['reservation_id'];
        $team->player_count = $request['player_count'];
        $team->name = $request['name'];
        $team->game_time = $request['game_time'];
        $team->game_time_rough = $request['game_time_rough'];
        $team->save();

        return new Response($team);
    }
}
