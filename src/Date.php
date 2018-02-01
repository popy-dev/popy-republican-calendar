<?php

namespace Popy\RepublicanCalendar;

/**
 * Republican date value object, for internal use.
 */
class Date
{
    /**
     * Republican year.
     * 
     * @var integer
     */
    protected $year;

    /**
     * Republican month.
     *
     * @var integer
     */
    protected $month;

    /**
     * Republican day.
     * 
     * @var integer.
     */
    protected $day;

    /**
     * Leap year flag.
     *
     * @var boolean
     */
    protected $leap;

    /**
     * Class constructor.
     *
     * @param integer $year
     * @param integer $month
     * @param integer $day
     */
    public function __construct($year, $month, $day, $isLeapYear = false)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->leap = $isLeapYear;
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
     * Is a leap year.
     * 
     * @return boolean
     */
    public function isLeap()
    {
        return $this->leap;
    }
}

