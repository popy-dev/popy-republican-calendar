<?php

namespace Popy\RepublicanCalendar\LeapYearCalculator;

use Popy\RepublicanCalendar\LeapYearCalculatorInterface;

/**
 * Modern leap day implementation.
 */
class Modern implements LeapYearCalculatorInterface
{
    /**
     * @inheritDoc
     */
    public function isLeapYear($year)
    {
        return !($year % 4) && ($year % 100)
            || !($year % 400)
        ;
    }
}
