<?php

namespace Popy\RepublicanCalendar\LeapYearCalculator;

use Popy\RepublicanCalendar\LeapYearCalculatorInterface;

/**
 * During the revolutionary calendar lifetime, leap years where 3, 7, 11, 15, 19
 * To reproduce this behavior, we just have to apply any calculation method with
 * year + 1
 */
class RommeWithContinuedImpairLeapDay implements LeapYearCalculatorInterface
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
        return $this->internal->isLeapYear($year + 1);
    }
}
