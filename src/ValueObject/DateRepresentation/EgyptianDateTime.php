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
     * Class constructor.
     *
     * @param integer $year     Year.
     * @param boolean $isLeap   Is a leap year.
     * @param integer $dayIndex Day index.
     */
    public function __construct($year, $isLeap, $dayIndex)
    {
        parent::__construct($year, $isLeap, $dayIndex);

        $this->month = intval($dayIndex / 30) + 1;
        $this->day = $dayIndex % 30 + 1;
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
