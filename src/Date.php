<?php

namespace Popy\RepublicanCalendar;

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
     * Class constructor.
     *
     * @param integer $year
     * @param integer $month
     * @param integer $day
     */
    public function __construct($year, $month, $day)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
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
}

