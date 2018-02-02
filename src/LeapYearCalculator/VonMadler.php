<?php

namespace Popy\RepublicanCalendar\LeapYearCalculator;

use Popy\RepublicanCalendar\LeapYearCalculatorInterface;

/**
 * Von Madler implementation (because why not being wrong in a funny way).
 */
class VonMadler implements LeapYearCalculatorInterface
{
    /**
     * @inheritDoc
     */
    public function isLeapYear($year)
    {
        return !($year % 4) && ($year % 128);
    }
}
