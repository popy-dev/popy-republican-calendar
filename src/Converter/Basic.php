<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTime;
use Popy\RepublicanCalendar\Date;
use Popy\RepublicanCalendar\Converter;

/**
 * Republican Date <=> DateTime converter interface.
 */
class Basic implements Converter
{
    /**
     * {@inheritDoc}
     */
    public function toRepublican(DateTime $input)
    {
        list($gregorianYear, $gregorianLeap, $dayIndex) = explode('-', $input->format('Y-L-z'));

        $year = $gregorianYear - 1792;

        $dayCount = 365 + $gregorianLeap;
        $dayIndex = (int)$dayIndex + 101;

        if ($dayIndex >= $dayCount) {
            $dayIndex = $dayIndex % $dayCount;
            $year++;
        }

        $month = intval($dayIndex / 30);
        $day = $dayIndex % 30;

        return new Date($year, $month + 1, $day + 1);
    }

    /**
     * {@inheritDoc}
     */
    public function fromRepublican(Date $input)
    {

    }
}