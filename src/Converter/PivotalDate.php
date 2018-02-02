<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeImmutable;
use DateTimeInterface;
use Popy\RepublicanCalendar\RepublicanDateTime;
use Popy\RepublicanCalendar\ConverterInterface;
use Popy\Calendar\Converter\TimeConverterInterface;
use Popy\Calendar\Converter\TimeConverter\DuoDecimalTime;
use Popy\Calendar\Converter\LeapYearCalculatorInterface;
use Popy\Calendar\Converter\AbstractPivotalDateSolarYear;
use Popy\RepublicanCalendar\Converter\LeapYearCalculator\RommeWithContinuedImpairLeapDay;


/**
 * Finest converter implementation i could make.
 */
class PivotalDate extends AbstractPivotalDateSolarYear implements ConverterInterface
{
    /**
     * Year 1 date (will result in a year 0 index, incremented to be displayed).
     *
     * 1792-09-22 00:00:00
     */
    const REVOLUTION_ERA_START = -5594227200;

    /**
     * Time converter.
     *
     * @var TimeConverterInterface
     */
    protected $timeConverter;

    /**
     * Class constructor.
     *
     * @param LeapYearCalculatorInterface|null $calculator    Leap year calculator.
     * @param TimeConverterInterface|null      $timeConverter Time converter.
     */
    public function __construct(LeapYearCalculatorInterface $calculator = null, TimeConverterInterface $timeConverter = null)
    {
        parent::__construct($calculator ?: new RommeWithContinuedImpairLeapDay());
        $this->timeConverter = $timeConverter ?: new DuoDecimalTime();
    }

    /**
     * @inheritDoc
     */
    protected function getEraStart()
    {
        return static::REVOLUTION_ERA_START;
    }

    /**
     * @inheritDoc
     */
    public function toRepublican(DateTimeInterface $input)
    {
        list($year, $dayIndex, $microsec, $offset) = $this->
            fromDateTimeInterface($input)
        ;

        $res = new RepublicanDateTime(
            $year,
            $dayIndex,
            $this->calculator->isLeapYear($year),
            $input->getTimezone(),
            $offset
        );

        return $res
            ->setTimestamp($input->getTimestamp())
            ->setTime(
                $this->timeConverter->fromMicroSeconds($microsec)
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function fromRepublican(RepublicanDateTime $input)
    {
        $microsec = $this->timeConverter->toMicroSeconds(
            $input->getTime()
        );

        return $this->toDateTimeInterface(
            $input->getYear(),
            $input->getDayIndex(),
            $microsec,
            $input->getTimezone()
        );
    }
}
