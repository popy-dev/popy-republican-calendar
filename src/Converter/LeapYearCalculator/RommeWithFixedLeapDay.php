<?php

namespace Popy\RepublicanCalendar\Converter\LeapYearCalculator;

use Popy\Calendar\Converter\LeapYearCalculator\Modern;
use Popy\Calendar\Converter\CompleteLeapYearCalculatorInterface;
use Popy\Calendar\Converter\LeapYearCalculator\AbstractCalculator;

/**
 * Implementation of a possible solution : at year 20, starts leaping a year
 * later, in order to have leaps on years 15, 19, 24
 */
class RommeWithFixedLeapDay implements CompleteLeapYearCalculatorInterface
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
        return $this->internal->isLeapYear($year < 21 ? ($year + 1) : $year);
    }

    /**
     * @inheritDoc
     */
    public function getYearLength($year)
    {
        return $this->internal->getYearLength($year < 21 ? ($year + 1) : $year);
    }

    /**
     * @inheritDoc
     */
    public function getYearEraDayIndex($year)
    {
        if ($year >= 21) {
            return $this->internal->getYearEraDayIndex($year);
        }

        return $this->internal->getYearEraDayIndex($year + 1)
            // Removes the added year
            - $this->internal->getYearLength(1)
        ;
    }

    /**
     * @inheritDoc
     */
    public function getYearAndDayIndexFromErayDayIndex($eraDayIndex)
    {
        $comp = $this->internal->getYearEraDayIndex(21);
        $mod = 0;

        // Before year 21
        if ($eraDayIndex < $comp) {
            // Adds a year
            $eraDayIndex += $this->internal->getYearLength(1);

            // and rem=ove it (later)
            $mod = 1;
        }

        $res = $this->internal->getYearAndDayIndexFromErayDayIndex($eraDayIndex);

        $res[0] -= $mod;

        return $res;
    }
}
