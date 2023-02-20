<?php

namespace App\Services;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * Singleton class for dealing with work days and holidays
 * 
 * Every time it requires \App\Models\Holiday models it will first check if it already fetched those model. If it didn't it will 
 * limmiting nnumber of queryes to 1. Since controller and validation rules require these calculation to be done, it made sence to store it in singleton.
 */
class WorkingDaysCalculator
{
    private static $instance = null;
    private $holidays = [];
    private bool $fetched = false;
    
    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new WorkingDaysCalculator();
        }

        return self::$instance;
    }

    public function getAllHolidays(): Collection
    {
        if($this->fetched){
            return $this->holidays;
        }

        $this->fetchHolidays();
        return $this->holidays;
    }

    private function fetchHolidays(): void
    {
        $this->holidays = Holiday::all();
        $this->fetched = true;
    }

    public function getAllHolidaysAsStrings(string $year = ''): array
    {
        $this->getAllHolidays();

        $stringifiedHolidays = [];
        $year = $year ? $year : self::getCurrentYear();

        foreach($this->holidays as $holiday){
            $stringifiedHolidays[] = Carbon::create($year, $holiday->month, $holiday->day)->toDateString();
        }

        return $stringifiedHolidays;
    }

    public static function getCurrentYear(): string
    {
        return strval(Carbon::now()->year);
    }

    /**
     * Based on @param $start and @param @end dates, calculates number of working days bewteen them. 
     * 
     * If @param $includeHolidays is true, it will include dates stored in database and non-working days.
     * 
     * @return int $workingDays
     */
    public function getWorkingDaysBetweenDates(string $start, string $end, bool $includeHolidays = true): int
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $start->startOfDay();
        $end->endOfDay();

        $workingDays = 0;

        while ($start <= $end) {
            // Check if the current day is a weekday and not a holiday
            if ($this->isWorkingDay($start, $includeHolidays ? $this->getAllHolidaysAsStrings() : [])) {
                $workingDays++;
            }
            
            $start->addDay();
        }
        
        return $workingDays;
    }

    private function isWorkingDay(Carbon $day, $holidays = [])
    {
        return $day->isWeekday() && !in_array($day->toDateString(), $holidays);
    }

    /**
     * Resets instance for testing purposes since tests apprently share same script...
     */
    public static function destroy(){
        self::$instance = null;
    } 
}