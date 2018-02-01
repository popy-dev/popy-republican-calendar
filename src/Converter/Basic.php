<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeInterface;
use Popy\RepublicanCalendar\RepublicanDateTime;
use Popy\RepublicanCalendar\ConverterInterface;

/**
 * Initial (bad) converter implementation.
 */
class Basic implements ConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function toRepublican(DateTimeInterface $input)
    {
        list($gregorianYear, $gregorianLeap, $dayIndex) = explode('-', $input->format('Y-L-z'));

        $year = $gregorianYear - 1792;

        $dayCount = 365 + $gregorianLeap;
        $dayIndex = (int)$dayIndex + 101;

        if ($dayIndex >= $dayCount) {
            $dayIndex = $dayIndex % $dayCount;
            $year++;
        }

        return new RepublicanDateTime($year, $dayIndex, $gregorianLeap, 0, 0, 0, $input);
    }

    /**
     * {@inheritDoc}
     */
    public function fromRepublican(RepublicanDateTime $input)
    {
        return $input->getDateTime();
    }
}