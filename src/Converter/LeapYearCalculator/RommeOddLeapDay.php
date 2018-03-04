<?php

namespace Popy\RepublicanCalendar\Converter\LeapYearCalculator;

use Popy\Calendar\Converter\LeapYearCalculator\Modern;
use Popy\Calendar\Converter\CompleteLeapYearCalculatorInterface;

/**
 * During the revolutionary calendar lifetime, leap years where 3, 7, 11, 15, 19
 * To reproduce this behavior, we just have to apply any calculation method with
 * year + 1
 */
class RommeOddLeapDay implements CompleteLeapYearCalculatorInterface
{
    /**
     * Internal calculation method
     *
     * @var CompleteLeapYearCalculatorInterface
     */
    protected $internal;

    /**
     * Class constructor.
     *
     * @param CompleteLeapYearCalculatorInterface|null $internal
     */
    public function __construct(CompleteLeapYearCalculatorInterface $internal = null)
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

    /**
     * @inheritDoc
     */
    public function getYearLength($year)
    {
        return $this->internal->getYearLength($year + 1);
    }

    /**
     * @inheritDoc
     */
    public function getYearEraDayIndex($year)
    {
        return $this->internal->getYearEraDayIndex($year + 1)
            // remove the added year
            - $this->internal->getYearLength($year)
        ;
    }

    /**
     * @inheritDoc
     */
    public function getYearAndDayIndexFromErayDayIndex($eraDayIndex)
    {
        $res = $this->internal->getYearAndDayIndexFromErayDayIndex(
            // add a year
            $eraDayIndex + $this->internal->getYearLength(1)
        );

        // then remove it
        $res[0]--;

        return $res;
    }
}
