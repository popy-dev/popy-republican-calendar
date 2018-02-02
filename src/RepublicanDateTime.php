<?php

namespace Popy\RepublicanCalendar;

use DateTimeZone;

/**
 * Republican date value object, for internal use.
 *
 * Keep in mind that this class is only a date representation. The actual and
 * trusted "time value" is the timestamp. A RepublicanDateTime may not know
 * the timestamp it represents.
 *
 * There are no internal constistency checks !
 */
class RepublicanDateTime
{
    /**
     * Year
     *
     * @var integer
     */
    protected $year;
    
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
     * Day Index
     *
     * @var integer
     */
    protected $dayIndex;

    /**
     * Time informations (hours, minutes, seconds, microseconds)
     *
     * @var array<int>
     */
    protected $time = [0, 0, 0, 0];

    /**
     * Timestamp : actual and trusted time representation.
     *
     * @var integer|null
     */
    protected $timestamp;

    /**
     * Timezone.
     *
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * TimeZone offset used when building this date object.
     *
     * @var integer
     */
    protected $offset;

    public function __construct($year, $dayIndex, $isLeap, DateTimeZone $timezone, $offset = 0)
    {
        $this->year = $year;
        $this->dayIndex = $dayIndex;
        $this->leap = $isLeap;

        $this->month = intval($dayIndex / 30) + 1;
        $this->day = $dayIndex % 30 + 1;

        $this->timezone = $timezone;
        $this->offset = $offset;
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
     * Get time informations.
     *
     * @return array<int>
     */
    public function getTime()
    {
        return $this->time;
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
     * Gets the Republican year.
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Gets the Republican month.
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Gets the Republican day.
     *
     * @return integer.
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Gets the Republican day index.
     *
     * @return integer.
     */
    public function getDayIndex()
    {
        return $this->dayIndex;
    }

    /**
     * Is a leap year.
     *
     * @return boolean
     */
    public function isLeap()
    {
        return $this->leap;
    }

    /**
     * Gets the Republican hours.
     *
     * @return integer.
     */
    public function getHours()
    {
        return $this->time[0];
    }

    /**
     * Gets the Republican minutes.
     *
     * @return integer.
     */
    public function getMinutes()
    {
        return $this->time[1];
    }

    /**
     * Gets the Republican seconds.
     *
     * @return integer.
     */
    public function getSeconds()
    {
        return $this->time[2];
    }

    /**
     * Get Republican microseconds.
     *
     * @return integer
     */
    public function getMicroseconds()
    {
        return $this->time[3];
    }

    /**
     * Gets the timestamp, if available.
     *
     * @return integer|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Gets timezone.
     *
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}
