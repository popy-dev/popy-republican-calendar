<?php

namespace Popy\RepublicanCalendar\Converter\UnixTimeConverter;

use Popy\Calendar\Converter\LeapYearCalculatorInterface;
use Popy\Calendar\ValueObject\DateFragmentedRepresentationInterface;
use Popy\Calendar\Converter\UnixTimeConverter\AbstractDatePartsSolarSplitter;

/**
 * Tri-Decimal monthes implementation.
 */
class TriDecimalMonthes extends AbstractDatePartsSolarSplitter
{
    /**
     * Leap year calculator.
     *
     * @var LeapYearCalculatorInterface
     */
    protected $calculator;

    /**
     * Class constructor.
     *
     * @param LeapYearCalculatorInterface $calculator Leap year calculator.
     */
    public function __construct(LeapYearCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @inheritDoc
     */
    protected function getAllFragmentSizes(DateFragmentedRepresentationInterface $input)
    {
        // $input->isLeapYear can't be trusted when parsing a date.
        $leap = $this->calculator->isLeapYear($input->getYear());
        $monthes = array_fill(0, 12, 30);
        $monthes[] = 5 + $leap;

        return [$monthes];
    }
}
