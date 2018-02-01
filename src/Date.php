<?php

namespace Popy\RepublicanCalendar;

use DateTimeInterface;

/**
 * Republican date value object, for internal use.
 */
class Date
{
    /**
     * Original/Real DateTime object.
     *
     * @var DateTimeInterface
     */
    protected $datetime;

    
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
     * Hours
     *
     * @var integer
     */
    protected $hour;
    
    /**
     * Minutes
     *
     * @var integer
     */
    protected $minute;
    
    /**
     * Seconds
     *
     * @var integer
     */
    protected $second;

    public function __construct($year, $dayIndex, $isLeap, $hour, $minute, $second, $microsecond, DateTimeInterface $datetime = null)
    {
        $this->year = $year;
        $this->dayIndex = $dayIndex;
        $this->leap = $isLeap;

        $this->month = intval($dayIndex / 30) + 1;
        $this->day = $dayIndex % 30 + 1;

        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
        $this->microsecond = $microsecond;

        // To be removed
        $this->datetime = $datetime;
    }

    /**
     * Gets the internal DateTime object.
     *
     * @return DateTimeInterface
     */
    public function getDateTime()
    {
        return $this->datetime;
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
     * Gets the Republican hour.
     *
     * @return integer.
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Gets the Republican minute.
     *
     * @return integer.
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Gets the Republican second.
     *
     * @return integer.
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * Get Republican microsecond.
     *
     * @return integer
     */
    public function getMicrosecond()
    {
        return $this->microsecond;
    }
}

