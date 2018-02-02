<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeImmutable;
use DateTimeInterface;
use Popy\RepublicanCalendar\RepublicanDateTime;
use Popy\RepublicanCalendar\ConverterInterface;
use Popy\Calendar\Converter\TimeConverterInterface;
use Popy\Calendar\Converter\TimeConverter\DuoDecimalTime;
use Popy\Calendar\Converter\LeapYearCalculatorInterface;
use Popy\RepublicanCalendar\Converter\LeapYearCalculator\RommeWithContinuedImpairLeapDay;

/**
 * Finest converter implementation i could make.
 */
class RelativeTimestampLeapYear implements ConverterInterface
{
    /**
     * Year 1 date (will result in a year 0 index, incremented to be displayed).
     *
     * 1792-09-22 00:00:00
     */
    const REVOLUTION_ERA_START = -5594227200;

    /**
     * Self-explanatory.
     */
    const SECONDS_PER_DAY = 24 * 3600;

    /**
     * Leap year calculator.
     *
     * @var LeapYearCalculatorInterface
     */
    protected $calculator;

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
        $this->calculator = $calculator ?: new RommeWithContinuedImpairLeapDay();
        $this->timeConverter = $timeConverter ?: new DuoDecimalTime();
    }

    /**
     * {@inheritDoc}
     */
    public function toRepublican(DateTimeInterface $input)
    {
        $offset = intval($input->format('Z'));
        // Use a timestamp relative to the first revolutionnary year and
        // including timezone offset
        $relativeTimestamp = $input->getTimestamp()
            - static::REVOLUTION_ERA_START
            + $offset
        ;

        $eraDayIndex = intval($relativeTimestamp / static::SECONDS_PER_DAY);
        $year = 1;

        // Will exit once the negative year will be found
        while ($eraDayIndex < 0) {
            $dayCount = 365 + $this->calculator->isLeapYear($year - 1);

            $eraDayIndex += $dayCount;
            $year--;
        }

        while (true) {
            $dayCount = 365 + $this->calculator->isLeapYear($year);

            if ($eraDayIndex < $dayCount) {
                // $year and dayIndex found !
                break;
            }

            $eraDayIndex -= $dayCount;
            $year++;
        }

        $remainingMicroSeconds = intval($input->format('u'))
            + ($relativeTimestamp % static::SECONDS_PER_DAY) * 1000000
        ;

        $res = new RepublicanDateTime(
            $year,
            $eraDayIndex,
            $this->calculator->isLeapYear($year),
            $input->getTimezone(),
            $offset
        );

        return $res
            ->setTimestamp($input->getTimestamp())
            ->setTime(
                $this->timeConverter->fromMicroSeconds($remainingMicroSeconds)
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function fromRepublican(RepublicanDateTime $input)
    {
        $dayIndex = $input->getDayIndex();
        $year = $input->getYear();

        $sign = $year < 1 ? -1 : 1;

        for ($i=min($year, 1); $i < max($year, 1); $i++) { 
            $dayCount = 365 + $this->calculator->isLeapYear($i);
            $dayIndex += $sign * $dayCount;
        }

        // todo timezone handling !
        $timestamp = static::REVOLUTION_ERA_START
            + ($dayIndex * static::SECONDS_PER_DAY)
        ;

        $remainingMicroSeconds = $this->timeConverter->toMicroSeconds(
            $input->getTime()
        );

        $timestamp += intval($remainingMicroSeconds) / 1000000;
        $microseconds = $remainingMicroSeconds % 1000000;

        // Looking for timezone offset matching the incomplete timestamp.
        // The LMT transition is skipped to mirror the behaviour of
        // DateTimeZone->getOffset()
        $offset = 0;
        $previous = null;
        $offsets = $input->getTimezone()->getTransitions(
            $timestamp-static::SECONDS_PER_DAY
        );
        foreach ($offsets as $info) {
            if (
                (!$previous || $previous['abbr'] !== 'LMT')
                && $timestamp - $info['offset'] < $info['ts']
            ) {
                break;
            }

            $previous = $info;

            $offset = $info['offset'];
        }

        $timestamp -= $offset;

        $timestring = sprintf(
            '%s.%06d UTC',
            $timestamp,
            $microseconds
        );

        return DateTimeImmutable::createFromFormat('U.u e', $timestring)
            ->setTimezone($input->getTimezone())
        ;
    }
}