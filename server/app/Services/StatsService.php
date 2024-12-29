<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;

/**
 * Prepare data fort stats and chart reports.
 */
class StatsService
{
    // Collection used to make stats.
    private Collection $dataset_collection;

    // Model property used as record timestamp (eg. created_at, date_from)
    private string $timestamp_property;

    // Range of dates for stats count.
    private Carbon $range_from;

    private Carbon $range_to;

    private string $resolution;

    const RESOLUTION_MONTH = 'month';

    const RESOLUTION_WEEK = 'week';

    const RESOLUTION_DAY = 'day';

    private array $chart_dates;

    /**
     * Specify collection and filtered_model_property.
     */
    public function __construct(Collection $collection, string $timestamp_property)
    {
        $this->dataset_collection = $collection;
        $this->timestamp_property = $timestamp_property;

        $this->range_from = Carbon::now()->startOfMonth();
        $this->range_to = Carbon::now()->endOfMonth();
        $this->resolution = 'day';
    }

    public function setRange(Carbon $range_from, Carbon $range_to)
    {
        $this->range_from = $range_from;
        $this->range_to = $range_to;
    }

    public function getRangeInDays(): int
    {
        return $this->range_from->diffInDays($this->range_to) + 1;
    }

    public function setResolution($resolution)
    {
        $this->resolution = $resolution;
    }

    public function prepareStats()
    {
        $this->chart_dates = $this->getChartDatesArray();
    }

    /**
     * Create array of all dates or date ranges,
     * which will appear in the generated chart.
     *
     * @return array Array of Carbon dates.
     */
    private function getChartDatesArray(): array
    {
        $dates = [];
        $i = $this->range_from->copy();

        while ($i <= $this->range_to) {
            if ($this->resolution == self::RESOLUTION_DAY) {
                $dates[] = $i;

                $i = $i->copy()->addDay();
            } elseif ($this->resolution == self::RESOLUTION_WEEK) {
                $dates[] = $i->startOfWeek();

                $i = $i->copy()->addWeek();
            } elseif ($this->resolution == self::RESOLUTION_MONTH) {
                $dates[] = $i->startOfMonth();
                $i = $i->copy()->addMonth();
            }
        }

        return $dates;
    }

    public function getChartLabels(): array
    {
        $labels = [];

        /* @var $chart_date Carbon */
        foreach ($this->chart_dates as $chart_date) {
            if ($this->resolution == self::RESOLUTION_DAY) {
                if ($chart_date->isWeekend()) {
                    $labels[] = $chart_date->format('j.').' '.$chart_date->minDayName;
                } else {
                    $labels[] = $chart_date->format('j.');
                }
            } elseif ($this->resolution == self::RESOLUTION_WEEK) {
                $labels[] = $chart_date->format('j.n').'-'.$chart_date->endOfWeek()->format('j.n');
            } elseif ($this->resolution == self::RESOLUTION_MONTH) {
                $labels[] = $chart_date->monthName;
            }
        }

        return $labels;
    }

    /**
     * @throws Exception
     */
    public function getChartValuesForModelCount(): array
    {
        $dataset_collection = $this->dataset_collection;

        $data = [];

        foreach ($this->chart_dates as $i) {
            if ($this->resolution == self::RESOLUTION_DAY) {
                $count = $dataset_collection->where($this->timestamp_property, '>=', $i->startOfDay())
                    ->where($this->timestamp_property, '<=', $i->endOfDay())
                    ->count();
            } elseif ($this->resolution == self::RESOLUTION_WEEK) {
                $count = $dataset_collection->where($this->timestamp_property, '>=', $i->startOfWeek())
                    ->where($this->timestamp_property, '<=', $i->endOfWeek())
                    ->count();

            } elseif ($this->resolution == self::RESOLUTION_MONTH) {
                $count = $dataset_collection->where($this->timestamp_property, '>=', $i->startOfMonth())
                    ->where($this->timestamp_property, '<=', $i->endOfMonth())
                    ->count();
            } else {
                throw new Exception('Invalid resolution specified.');
            }

            $data[] = $count;
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function getChartValuesPropertySum(string $property_name): array
    {
        $dataset_collection = $this->dataset_collection;

        $data = [];

        foreach ($this->chart_dates as $i) {
            if ($this->resolution == self::RESOLUTION_DAY) {
                $sum = $dataset_collection->where($this->timestamp_property, '>=', $i->startOfDay())
                    ->where($this->timestamp_property, '<=', $i->endOfDay())
                    ->sum($property_name);
            } elseif ($this->resolution == self::RESOLUTION_WEEK) {
                $sum = $dataset_collection->where($this->timestamp_property, '>=', $i->startOfWeek())
                    ->where($this->timestamp_property, '<=', $i->endOfWeek())
                    ->sum($property_name);

            } elseif ($this->resolution == self::RESOLUTION_MONTH) {
                $sum = $dataset_collection->where($this->timestamp_property, '>=', $i->startOfMonth())
                    ->where($this->timestamp_property, '<=', $i->endOfMonth())
                    ->sum($property_name);
            } else {
                throw new Exception('Invalid resolution specified.');
            }

            $data[] = $sum;
        }

        return $data;
    }
}
