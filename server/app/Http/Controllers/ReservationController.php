<?php

namespace App\Http\Controllers;

use App\Jobs\SyncReadinessTimesWithReservations;
use App\Models\AffiliateCode;
use App\Models\ReadinessTime;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\Reservation\CustomerReservationStored;
use App\Reservation\Interval;
use App\Services\CustomerService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Uid\Uuid;

class ReservationController extends Controller
{
    /**
     * Step 1 - choose date.
     */
    public function reservationHowMuchPeople(Request $request)
    {
        return redirect('/', 301);

        return view('reservation.step_1', [
            'count' => $request->input('count'),
        ]);
    }

    /**
     * Step 2 - choose date.
     *
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function reservationChooseDay(Request $request)
    {
        return redirect('/', 301);

        $date = $request->input('date');
        $reserved_people_count = $request->input('count');

        if ($reserved_people_count == null) {
            abort(404);
        }

        if (! $date) {
            $start_of_month = Carbon::today()->startOfMonth();
        } else {
            $start_of_month = Carbon::parse($date)->startOfMonth();
        }

        if ($start_of_month < Carbon::now()->startOfMonth()) {
            return redirect()->route('reservation.start');
        }

        $days = $this->getAvailableDays($start_of_month);

        return view('reservation.step_2', [
            'days' => $days,
            'count' => $reserved_people_count,
            'date_from' => $start_of_month,
            'date_to' => $start_of_month->copy()->endOfMonth(),
        ]);
    }

    /**
     * Step 3 - choose time.
     *
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function reservationChooseTimes(Request $request)
    {
        return redirect('/', 301);

        $count = $request->input('count');

        // Validate selected date query parameter.
        $date_string = $request->input('date');

        if ($count == null) {
            abort(404);
        }

        if (! $date_string) {
            return redirect()->route('reservation.start');
        }

        // Process request
        $date = Carbon::parse($date_string);

        $intervals = $this->getAvailableReservationStartIntervals($date);

        // Filter available intervals for this day
        $available_intervals = $intervals->filter(function (Interval $interval) use ($count) {
            $open = $interval->isOpened();
            $player_count_available = $interval->getAvailablePlayerCount() >= $count;
            $start_time_is_at_least_hour_from_now = $interval->time >= Carbon::now()->addMinutes(60);

            return $open && $player_count_available && $start_time_is_at_least_hour_from_now;
        });

        $count_string = $this->getCountString($count);

        return view('reservation.step_3', [
            'available_intervals' => $available_intervals,
            'date' => $date,
            'count' => $count,
            'count_string' => $count_string,
        ]);
    }

    /**
     * Step 4 - user data.
     *
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function reservationForm(Request $request)
    {
        return redirect('/', 301);

        // Validate selected date query parameter.
        $date_string = $request->input('date');

        if (! $date_string) {
            return redirect()->route('reservation.start');
        }

        // Process request
        $time = Carbon::parse($date_string);
        $count = $request->input('count');

        return view('reservation.step_4', [
            'time' => $time,
            'count' => $count,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function reservationSubmit(Request $request)
    {
        // Validate
        //        $this->validate($request, [
        //            'g-recaptcha-response' => 'required|recaptcha',
        //        ]);

        $customer = (new CustomerService)->findOrCreate($request['email'], $request['phone']);

        // Process submit
        $reservation = new Reservation;
        $reservation->uuid = Uuid::v4();
        $reservation->date_from = Carbon::parse($request->input('date'));
        $reservation->date_to = $reservation->date_from->copy()->addHours(3);
        $reservation->team_count = $request->input('team_count');
        $reservation->customer_id = $customer->id;
        $reservation->name = '-';
        $reservation->email = $request->input('email');
        $reservation->phone = $request->input('phone');
        $reservation->description = $request->input('description');

        if ($request->hasCookie('vb_aw')) {
            $reservation->cookie_gcl_aw = $request->cookie('vb_aw');
        }

        $reservation->save();

        if ($request->hasCookie('vb_aw')) {
            $reservation->campaigns()->attach(6);
        }

        if ($request->input('discount_code')) {
            $code = AffiliateCode::where('code', $request->input('discount_code'))
                ->firstOrFail();

            $reservation->affiliate_code_id = $code->id;
            $reservation->save();

            $reservation->affiliate_partners()->attach($code->affiliate_partner_id);
        }

        $reservation->notify((new CustomerReservationStored));
        SyncReadinessTimesWithReservations::dispatch();

        return redirect()->route('reservation.public', [
            'uuid' => $reservation->uuid,
        ])->with('submitted', 1);
    }

    public function reservationPublic($uuid, Request $request)
    {
        $reservation = Reservation::where('uuid', $uuid)->firstOrFail();

        if ($request->hasCookie('vb_aw')) {
            $reservation->cookie_gcl_aw = $request->cookie('vb_aw');
            $reservation->save();
        }

        return view('reservation.detail', [
            'reservation' => $reservation,
        ]);
    }

    public function reservationFindTimesApi(Request $request)
    {
        $count = $request->input('count');

        // Validate selected date query parameter.
        $date_string = $request->input('date');

        if ($count == null || $date_string == null) {
            abort(404);
        }

        // Process request
        $date = Carbon::parse($date_string);

        $intervals = $this->getAvailableReservationStartIntervals($date);

        // Filter available intervals for this day
        $available_intervals = $intervals->filter(function (Interval $interval) use ($count) {
            $open = $interval->isOpened();
            $player_count_available = $interval->getAvailablePlayerCount() >= $count;
            $start_time_is_at_least_hour_from_now = $interval->time >= Carbon::now()->addMinutes(60);

            return $open && $player_count_available && $start_time_is_at_least_hour_from_now;
        });

        $available_intervals->map(function (Interval $interval) {
            $interval->available_seats = $interval->getAvailablePlayerCount();
            $interval->used_seats = $interval->getCurrentPlayerCount();
        });

        $count_string = $this->getCountString($count);

        $data = [
            'available_intervals' => $available_intervals,
            'date' => $date,
            'count' => $count,
            'count_string' => $count_string,
            'available_intervals_count' => $available_intervals->count(),
        ];

        return response($data);
    }

    private function getAvailableReservationStartIntervals(Carbon $date)
    {
        $start_of_day = $date->copy()->startOfDay();
        $end_of_day = $date->copy()->endOfDay();

        $today_readiness_times = ReadinessTime::where('date_from', '>=', $start_of_day)
            ->where('date_from', '<', $end_of_day)
            ->where(function ($query) {
                $query
                    ->where('date_from', '>', Carbon::now()->addDays(2))
                    ->orWhere('user_id', '!=', null);
            })
            ->get();

        // Prepare colelction of all today intervals
        $today_reservations = Reservation::where('date_from', '>=', $start_of_day)
            ->where('date_from', '<', $end_of_day)
            ->get();

        $times = [];
        $time = $start_of_day->copy();

        do {
            $related_reservations = $today_reservations->where('date_from', '<=', $time->copy()->addHours(2)->addMinutes(31))
                ->where('date_to', '>', $time)
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'not_arived');

            $times[] = new Interval($time, $related_reservations, $today_readiness_times);

            $time = $time->copy()->addMinutes(30);
        } while ($time < $end_of_day);

        return collect($times);
    }

    private function getAvailableDays(Carbon $month): array
    {
        $days = [];

        $start_of_month = $month->copy()->startOfMonth();
        $end_of_month = $month->copy()->endOfMonth();

        // Check readiness times available this month
        $this_month_opening_times = ReadinessTime::where('date_from', '>=', $start_of_month)
            ->where('date_to', '<', $end_of_month)
            ->where(function ($query) {
                $query
                    ->where('date_from', '>', Carbon::now()->addDays(2))
                    ->orWhere('user_id', '!=', null);
            })
            ->get();

        // Prepare array of available days
        $day = $start_of_month->copy();

        do {
            $opened = $this_month_opening_times->where('date_from', '>=', $day)
                ->where('date_to', '<', $day->copy()->addDay())
                ->count();

            $days[] = [
                'date' => $day,
                'opened' => $opened,
            ];

            $day = $day->copy()->addDay();
        } while ($day < $end_of_month);

        return $days;
    }

    private function getAdmins()
    {
        return User::all();
    }

    private function getCountString(int $count): string
    {
        if ($count <= 0) {
            return '0 lidí';
        }

        if ($count == 1) {
            return 'vešel '.$count.' člověk';
        } elseif ($count <= 4) {
            return 'vešli '.$count.' lidé';
        } else {
            return 'vešlo '.$count.' lidí';
        }
    }
}
