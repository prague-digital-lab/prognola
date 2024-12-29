<?php

namespace App\Http\Controllers;

use App\Models\ReadinessTime;
use App\Models\Reservation;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeZone as PhpDateTimeZone;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

class CalendarController extends Controller
{
    public function ical()
    {
        $phpDateTimeZone = new PhpDateTimeZone('Europe/Prague');

        $reservations = Reservation::where('date_from', '>', Carbon::now()->startOfMonth()->subMonth())
            ->where('date_from', '<=', Carbon::now()->startOfMonth()->addMonths(2))
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED, Reservation::STATUS_NOT_ARRIVED])
            ->with('readiness_times.user')
            ->get();

        $events = [];

        foreach ($reservations as $reservation) {
            $team_count_string = $reservation->getTeamCountString();

            $url = route('admin.reservations.show', $reservation);
            $description = <<<DESC
            Počet: $team_count_string
            Email: $reservation->email
            Telefon: $reservation->phone
            Odkaz do IS: $url
            DESC;

            $user_name_arr = $reservation->readiness_times->where('user_id', '!=', null)->pluck('user.name')->toArray();
            $user_names = array_unique($user_name_arr);

            if (count($user_names) > 0) {
                $user_names = implode(', ', $user_names);
            } else {
                $user_names = 'NEPOKRYTO';
            }

            $events[] = (new Event)
                ->setSummary($user_names.' - '.$team_count_string)
                ->setDescription($description)
                ->setOccurrence(
                    new TimeSpan(
                        new DateTime(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $reservation->date_from->format('Y-m-d H:i:s'), $phpDateTimeZone), true),
                        new DateTime(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $reservation->date_to->format('Y-m-d H:i:s'), $phpDateTimeZone), true)
                    )
                );
        }

        // 2. Create Calendar domain entity
        $calendar = new Calendar($events);

        // 3. Transform domain entity into an iCalendar component
        $componentFactory = new CalendarFactory;
        $calendarComponent = $componentFactory->createCalendar($calendar);

        // 4. Set headers
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');

        return $calendarComponent;
    }

    public function icalAllReadinessTimes()
    {
        $phpDateTimeZone = new PhpDateTimeZone('Europe/Prague');

        $readiness_times = ReadinessTime::where('date_from', '>', Carbon::now()->startOfMonth()->subMonth())
            ->where('date_from', '<=', Carbon::now()->startOfMonth()->addMonths(2))
            ->get();

        $events = [];

        foreach ($readiness_times as $readiness_time) {
            $url = route('admin.readiness_times.show', $readiness_time);
            $user_name = $readiness_time->user ? $readiness_time->user->name : '-';
            $user_phone = $readiness_time->user ? $readiness_time->user->phone : '-';
            $description = <<<DESC
            Informace o směně
            _____________________________________
            Recepční: $user_name
            Telefon: $user_phone
            Odkaz do IS: $url
            DESC;

            $time_title = $readiness_time->reservations()->count() > 0 ? 'AKTIVNÍ SMĚNA' : 'volná směna';
            $user_name = $readiness_time->user ? $readiness_time->user->name : 'NEPŘIŘAZENA';

            $events[] = (new Event)
                ->setSummary($time_title.' - '.$user_name)
                ->setDescription($description)
                ->setOccurrence(
                    new TimeSpan(
                        new DateTime(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $readiness_time->date_from->format('Y-m-d H:i:s'), $phpDateTimeZone), true),
                        new DateTime(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $readiness_time->date_to->format('Y-m-d H:i:s'), $phpDateTimeZone), true)
                    )
                );
        }

        // 2. Create Calendar domain entity
        $calendar = new Calendar($events);

        // 3. Transform domain entity into an iCalendar component
        $componentFactory = new CalendarFactory;
        $calendarComponent = $componentFactory->createCalendar($calendar);

        // 4. Set headers
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');

        return $calendarComponent;
    }
}
