<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeInterface;
use Popy\Calendar\Converter\TimeConverterInterface;
use Popy\Calendar\Converter\LeapYearCalculator\NoLeap;
use Popy\Calendar\Converter\TimeConverter\DecimalTime;
use Popy\Calendar\Converter\LeapYearCalculatorInterface;
use Popy\Calendar\Converter\AbstractPivotalDateSolarYear;
use Popy\RepublicanCalendar\ValueObject\DateRepresentation\EgyptianDateTime;

/**
 * Egyptian calendar implementation.
 *
 * Dependencies defaults to NoLeap Leap calculator and DecimalTime TimeConverter
 * to mimic historical behaviour.
 *
 * @link https://en.wikipedia.org/wiki/Egyptian_calendar
 */
class EgyptianPivotalDate extends AbstractPivotalDateSolarYear
{
    /**
     * Year 1 timestamp. (to be confirmed)
     *
     * -2781-07-19 00:00:00 UTC
     */
    protected $eraStart = -149909875200;

    /**
     * Class constructor.
     *
     * @param LeapYearCalculatorInterface|null $calculator    Leap year calculator.
     * @param TimeConverterInterface|null      $timeConverter Time converter.
     * @param integer|null                     $eraStart      Era starting date.
     */
    public function __construct(LeapYearCalculatorInterface $calculator = null, TimeConverterInterface $timeConverter = null, $eraStart = null)
    {
        parent::__construct(
            $calculator ?: new NoLeap(),
            $timeConverter ?: new DecimalTime()
        );

        if ($eraStart !== null) {
            $this->eraStart = $eraStart;
        }
    }

    /**
     * @inheritDoc
     */
    protected function getEraStart()
    {
        return $this->eraStart;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildDateRepresentation(DateTimeInterface $input, $year, $isLeapYear, $dayIndex)
    {
        return new EgyptianDateTime(
            $year,
            $isLeapYear,
            $dayIndex
        );
    }
}
