<?php

namespace Popy\RepublicanCalendar\Converter\LeapYearCalculator;

use Popy\Calendar\Converter\LeapYearCalculatorInterface;
use Popy\Calendar\Converter\LeapYearCalculator\Modern;

/**
 * Implementation of a possible solution : at year 20, starts leaping a year
 * later.
 */
class RommeWithFixedLeapDay implements LeapYearCalculatorInterface
{
    /**
     * Internal calculation method
     *
     * @var LeapYearCalculatorInterface
     */
    protected $internal;

    /**
     * Class constructor.
     *
     * @param LeapYearCalculatorInterface|null $internal
     */
    public function __construct(LeapYearCalculatorInterface $internal = null)
    {
        $this->internal = $internal ?: new Modern();
    }

    /**
     * @inheritDoc
     */
    public function isLeapYear($year)
    {
        return $this->internal->isLeapYear($year < 19 ? ($year + 1) : $year);
    }
}
