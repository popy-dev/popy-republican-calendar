<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeInterface;
use Popy\Calendar\Converter\TimeConverterInterface;
use Popy\Calendar\Converter\LeapYearCalculatorInterface;
use Popy\Calendar\Converter\TimeConverter\DuoDecimalTime;
use Popy\Calendar\Converter\AbstractPivotalDateSolarYear;
use Popy\RepublicanCalendar\Converter\LeapYearCalculator\RommeWithContinuedImpairLeapDay;

/**
 * French revolutionnary/republican implementation.
 *
 * Dependencies defaults to a RommeWithContinuedImpairLeapDay calculator (
 * respecting the initial 20 years leaps) and a DuoDecimalTime time converter
 *
 * @link https://en.wikipedia.org/wiki/French_Republican_Calendar
 */
class RepublicanPivotalDate extends EgyptianPivotalDate
{
    /**
     * Year 1 timestamp.
     *
     * 1792-09-22 00:00:00 UTC
     */
    protected $eraStart = -5594227200;

    /**
     * Class constructor.
     *
     * @param LeapYearCalculatorInterface|null $calculator    Leap year calculator.
     * @param TimeConverterInterface|null      $timeConverter Time converter.
     */
    public function __construct(LeapYearCalculatorInterface $calculator = null, TimeConverterInterface $timeConverter = null)
    {
        parent::__construct(
            $calculator ?: new RommeWithContinuedImpairLeapDay(),
            $timeConverter ?: new DuoDecimalTime()
        );
    }
}
