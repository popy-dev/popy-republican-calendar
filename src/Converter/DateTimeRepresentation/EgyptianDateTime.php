<?php

namespace Popy\RepublicanCalendar\Converter\DateTimeRepresentation;

use DateTimeZone;
use Popy\Calendar\Converter\DateTimeRepresentation\AbstractSolarTime;

/**
 * Egyptian date representation value object, for internal use.
 *
 * There are no internal constistency checks !
 */
class EgyptianDateTime extends AbstractSolarTime
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
     * @param integer      $year     Year.
     * @param integer      $dayIndex Day index.
     * @param integer      $isLeap   Is a leap year.
     * @param DateTimeZone $timezone Time zone.
     * @param integer      $offset   Time offset.
     */
    public function __construct($year, $dayIndex, $isLeap, DateTimeZone $timezone, $offset = 0)
    {
        $this->time = [0, 0, 0, 0];

        $this->year = (int)$year;
        $this->dayIndex = (int)$dayIndex;
        $this->leapYear = (bool)$isLeap;

        $this->timezone = $timezone;
        $this->offset = $offset;

        $this->month = intval($dayIndex / 30) + 1;
        $this->day = $dayIndex % 30 + 1;
    }

    /**
     * Set time information.
     *
     * @param array<int> $time
     */
    public function setTime(array $time)
    {
        $res = clone $this;

        $res->time = $time + [0, 0, 0, 0];

        return $res;
    }

    /**
     * Sets the timestamp.
     *
     * @param integer $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $res = clone $this;

        $res->timestamp = $timestamp;

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

    /**
     * Gets the hours.
     *
     * @return integer.
     */
    public function getHours()
    {
        return $this->time[0];
    }

    /**
     * Gets the minutes.
     *
     * @return integer.
     */
    public function getMinutes()
    {
        return $this->time[1];
    }

    /**
     * Gets seconds.
     *
     * @return integer.
     */
    public function getSeconds()
    {
        return $this->time[2];
    }

    /**
     * Gets microseconds.
     *
     * @return integer
     */
    public function getMicroseconds()
    {
        return $this->time[3];
    }
}
