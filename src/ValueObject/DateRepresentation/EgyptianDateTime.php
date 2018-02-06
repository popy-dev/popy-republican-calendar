<?php

namespace Popy\RepublicanCalendar\ValueObject\DateRepresentation;

use DateTimeZone;
use Popy\Calendar\ValueObject\DateRepresentation\SolarTime;

/**
 * Egyptian date representation value object, for internal use.
 *
 * Handles (for now) hardcoded month calculation.
 */
class EgyptianDateTime extends SolarTime
{
    /**
     * Month
     *
     * @var integer
     */
    protected $month;
    
    /**
     * Day
     *
     * @var integer
     */
    protected $day;

    /**
     * @inheritDoc
     */
    public function withDayIndex($dayIndex, $eraDayIndex)
    {
        $res = parent::withDayIndex($dayIndex, $eraDayIndex);

        $res->month = intval($dayIndex / 30) + 1;
        $res->day = $dayIndex % 30 + 1;

        return $res;
    }

    /**
     * Gets the month number.
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Gets the day number.
     *
     * @return integer.
     */
    public function getDay()
    {
        return $this->day;
    }
}
