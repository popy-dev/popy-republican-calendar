<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use Popy\RepublicanCalendar\RepublicanDateTime;
use Popy\RepublicanCalendar\ConverterInterface;
use Popy\RepublicanCalendar\LeapYearCalculatorInterface;
use Popy\RepublicanCalendar\LeapYearCalculator\Modern;


use Popy\RepublicanCalendar\Utility\TimeConverter as TimeConverterUtility;

/**
 * Finest converter implementation i could make.
 */
class RelativeTimestampLeapYear implements ConverterInterface
{
    /**
     * Year 1 date (will result in a year 0 index, incremented to be displayed)
     */
    const REVOLUTION_ERA_START = '1792-09-22 00:00:00';
    const REVOLUTION_ERA_DATE_FORMAT = 'Y-m-d H:i:s';

    const SECONDS_PER_DAY = 24 * 3600;

    /**
     * Leap year calculator.
     *
     * @var LeapYearCalculatorInterface
     */
    protected $calculator;

    /**
     * Class constructor.
     *
     * @param LeapYearCalculatorInterface|null $calculator Leap year calculator.
     */
    public function __construct(LeapYearCalculatorInterface $calculator = null)
    {
        $this->calculator = $calculator ?: new Modern();
    }

    /**
     * {@inheritDoc}
     */
    public function toRepublican(DateTimeInterface $input)
    {
        // Use a timestamp relative to the first revolutionnary year and
        // including timezone offset
        $relativeTimestamp = $input->getTimestamp()
            - $this->getRevolutionEraStart($input->getTimeZone())
            + intval($input->format('Z'))
        ;

        $offsets = $input->getTimeZone()->getTransitions(
            $input->getTimestamp()-static::SECONDS_PER_DAY, 
            $input->getTimestamp()+static::SECONDS_PER_DAY
        );

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

        $c = new TimeConverterUtility();

        $remainingSeconds = $relativeTimestamp % static::SECONDS_PER_DAY;
        $time = $c->getTimeFromLowerUnityCount($remainingSeconds, [24, 60, 60]);

        $res = new RepublicanDateTime($year, $eraDayIndex, $this->calculator->isLeapYear($year), $input);

        $res = $res->setTime($time[0], $time[1], $time[2]);

        return $res;
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
        $timestamp = $this->getRevolutionEraStart($input->getTimeZone())
            + ($dayIndex * static::SECONDS_PER_DAY)
        ;

        $c = new TimeConverterUtility();

        $timestamp += $c->getLowerUnityCountFromTime(
            [$input->getHour(), $input->getMinute(), $input->getSecond()],
            [24, 60, 60]
        );

        // Looking for timezone offset matching the incomplete timestamp.
        // The LMT transition is skipped to mirror the behaviour of
        // DateTimeZone->getOffset()
        $offset = 0;
        $previous = null;
        $offsets = $input->getTimeZone()->getTransitions(
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

        return DateTimeImmutable::createFromFormat(
                'U e',
                $timestamp . ' UTC'
            )
            ->setTimezone($input->getTimeZone())
        ;
    }

    /**
     * Instanciates a RevolutionEraStart date for the given DateTimeZone, then
     *    returns its timestamp. The DateTimeZone is here so that the
     *    DateTimeIMmutable object is able to initialize properly on the same
     *    timezone than the related date;
     *
     * @param DateTimeZone $timezone
     *
     * @return DateTimeImmutable
     */
    protected function getRevolutionEraStart(DateTimeZone $timezone)
    {
        return
            DateTimeImmutable::createFromFormat(
                static::REVOLUTION_ERA_DATE_FORMAT,
                static::REVOLUTION_ERA_START,
                $timezone
            )
            ->getTimestamp()
        ;
    }
}