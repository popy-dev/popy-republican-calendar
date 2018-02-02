<?php

namespace Popy\RepublicanCalendar;

/**
 * Leap year calculator.
 */
interface LeapYearCalculatorInterface
{
    /**
     * Determines if input year is a leap year.
     *
     * @param integer $year
     *
     * @return boolean
     */
    public function isLeapYear($year);
}