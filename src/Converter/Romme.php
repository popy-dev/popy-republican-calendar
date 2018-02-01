<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use Popy\RepublicanCalendar\Date;
use Popy\RepublicanCalendar\ConverterInterface;

/**
 * Romme implementation, adapted from caarmen's implementation
 *
 * @link https://github.com/caarmen/french-revolutionary-calendar
 */
class Romme implements ConverterInterface
{
    const REVOLUTION_ERA_END = '1811-09-23 00:00:00';
    const REVOLUTION_ERA_END_FORMAT = 'Y-m-d H:i:s';

    /**
     * Instanciates a RevolutionEraEnd date for the given DateTimeZone
     *
     * @param DateTimeZone $timezone
     *
     * @return DateTimeImmutable
     */
    protected function getRevolutionEraEnd(DateTimeZone $timezone)
    {
        return DateTimeImmutable::createFromFormat(
            static::REVOLUTION_ERA_END_FORMAT,
            static::REVOLUTION_ERA_END,
            $timezone
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toRepublican(DateTimeInterface $input)
    {
        $frenchEraEnd = $this->getRevolutionEraEnd($input->getTimezone());

        // Time elapsed between the end of the French calendar and the given
        // date. We have to include the daylight savings offset, because back in
        // 1792-1811,
        // daylight savings time wasn't being used. If we don't take into
        // account the offset, a calculation like 8/5/1996 00:00:00 - 8/5/1796
        // 00:00:00 will not return 200 years, but 200 years - 1 hour, which is
        // not the desired result.
        $numSecondsSinceEndOfFrenchEra =
            $input->getTimestamp() + $input->format('Z')
            - $frenchEraEnd->getTimestamp() - $frenchEraEnd->format('Z')
        ;

        // The Romme method applies the same
        // rules (mostly) of the Gregorian calendar to the French calendar.
        // One difference is the year 4000, which is a leap year in the
        // Gregorian system, but not in the French system. For now we ignore
        // this difference. We may address it and fix this code in the year
        // 3999 :)

        // Create a fake calendar object (fake because this is not really a
        // Gregorian date), corresponding to the end of the French calendar era.
        // This was in the French year 20. Since in the Gregorian year 20, there
        // were no leap years yet, we add 2000 to the year, so that the
        // Gregorian calendar implementation can handle the leap years.

        // The end of the French calendar system was the beginning of the year
        // 20.
        $fakeEndFrenchEraTimestamp = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            '2020-01-01 00:00:00',
            $input->getTimezone()
        )->getTimestamp();

        // Add the elapsed time to the French date.
        $fakeFrenchTimestamp = $fakeEndFrenchEraTimestamp + $numSecondsSinceEndOfFrenchEra;

        // Create a calendar object for the French date
        $fakeFrenchDate = new DateTimeImmutable('now', $input->getTimezone());
        $fakeFrenchDate = $fakeFrenchDate->setTimeStamp($fakeFrenchTimestamp);

        // Extract the year, leap and day in year from the French date.
        list($year, $leap, $dayIndex) = explode('-', $fakeFrenchDate->format('Y-L-z'));

        $year = $year - 2000;

        $time = $this->toRepublicanTime($input);

        return new Date($year, $dayIndex, $leap, $time[0], $time[1], $time[2], $time[3], $input);
    }

    /**
     * Converts a regular time (H:i:s) into a republican time (as array).
     *
     * @param DateTimeInterface $input
     *
     * @return array [hours, minutes, seconds]
     */
    public function toRepublicanTime(DateTimeInterface $input)
    {
        list($hour, $min, $seconds, $micro) = explode(':', $input->format('H:i:s:u'));

        $dayFraction = $this->getDayFractionFromTime([$hour, $min, $seconds, $micro], [24, 60, 60, 1000000]);

        return $this->getTimeFromDayFraction($dayFraction, [10, 100, 100, 1000000]);
    }

    /**
     * {@inheritDoc}
     */
    public function fromRepublican(Date $input)
    {
        $frenchEraEnd = $this->getRevolutionEraEnd($input->getTimezone());

        $time = $this->fromRepublicanTime($input);

        // Create a fake calendar object (fake because this is not really a
        // Gregorian date), corresponding to the end of the French calendar era.
        // This was in the French year 20. Since in the Gregorian year 20, there
        // were no leap years yet, we add 2000 to the year, so that the
        // Gregorian calendar implementation can handle the leap years.
        $fakeEndFrenchEraEndCalendar = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            '2020-01-01 00:00:00',
            $input->getTimezone()
        );

        // Create a fake calendar object for the given French date
        $fakeFrenchCalendar = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            sprintf(
                '%s-01-01 00:00:00',
                2000 + $input->getYear()
            ),
            $input->getTimezone()
        );

        if ($input->getDayIndex()) {
            $fakeFrenchCalendar = $fakeFrenchCalendar->modify(sprintf(
                '+ %s day',
                $input->getDayIndex()
            ));
        }

        // Determine how much time passed since the end of the French calendar
        //    (its year 20, Gregorian year 1811) and the given French date
        $numSecondsSinceEndOfFrenchEra =
            $fakeFrenchCalendar->getTimestamp() + $fakeFrenchCalendar->format('Z')
            - $fakeEndFrenchEraEndCalendar->getTimestamp() - $fakeEndFrenchEraEndCalendar->format('Z')
        ;

        // Create the Gregorian calendar object starting with 1811 and adding
        //    this time passed
        $result = DateTimeImmutable::createFromFormat(
            'U.u',
            sprintf(
                '%s.%s',
                $frenchEraEnd->getTimestamp() + $numSecondsSinceEndOfFrenchEra,
                $time[3]
            ),
            $input->getTimezone()
        );

        // We could have added the getLowerUnityCountFromTime() result to the
        //    previously calculated timestamp, by I'm not quite sure that the
        //    calculated timestamp will always start at 00:00:00 because of
        //    day light savings.
        $result = $result->setTime($time[0], $time[1], $time[2]);

        return $result;
    }

    /**
     * Converts a republican time (H:i:s) into a regular time (as array).
     *
     * @param Date $input
     *
     * @return array [hours, minutes, seconds]
     */
    public function fromRepublicanTime(Date $input)
    {
        $dayFraction = $this->getDayFractionFromTime(
            [$input->getHour(), $input->getMinute(), $input->getSecond(), $input->getMicrosecond()],
            [10, 100, 100, 1000000]
        );

        return $this->getTimeFromDayFraction($dayFraction, [24, 60, 60, 1000000]);
    }

    /**
     * Converts a "Time" (represented by an array of each of its constituents)
     *     into a fraction of a day, based on the constituents ranges.
     * 
     * @param array $timeParts     Time constituents array.
     * @param array $fractionSizes Time constituants ranges.
     *
     * @return float
     */
    protected function getDayFractionFromTime(array $timeParts, array $fractionSizes)
    {
        $len = count($fractionSizes);
        $fraction = 0;

        for ($i = count($timeParts) - 1; $i > -1; $i--) {
            $part = isset($timeParts[$i]) ? $timeParts[$i] : 0;
            $fraction = ($fraction + $part) / $fractionSizes[$i];
        }

        return $fraction;
    }

    /**
     * Converts a "Time" (represented by an array of each of its constituents)
     *     into the lowest of its defined units (usefull if you want, for
     *     instance, to convert a [h,m,s,u] into seconds)
     * 
     * @param array $timeParts     Time constituents array.
     * @param array $fractionSizes Time constituants ranges.
     *
     * @return integer
     */
    protected function getLowerUnityCountFromTime(array $timeParts, array $fractionSizes)
    {
        $len = count($fractionSizes);
        $res = 0;
   
        for ($i=0; $i < $len; $i++) {
            $part = isset($timeParts[$i]) ? $timeParts[$i] : 0;
            $res = $res * $fractionSizes[$i] + $part;
        }

        return $res;
    }

    /**
     * Converts a dayFraction onto a "Time" (represented by an array of each of
     *     its constituents) based on the constituents ranges.
     *
     * @param float $dayFraction   Day fraction.
     * @param array $fractionSizes Time constituants ranges.
     *
     * @return array
     */
    protected function getTimeFromDayFraction($dayFraction, array $fractionSizes)
    {
        $res = [];
        $len = count($fractionSizes);

        for ($i=0; $i < $len; $i++) { 
            $dayFraction = $dayFraction * $fractionSizes[$i];

            if ($i + 1 < $len) { 
                $dayFraction = $dayFraction - ($res[] = (int)$dayFraction);
            } else {
                // Rounding last value to avoid loosing data
                $res[] = round($dayFraction);
            }
        }

        for ($i=$len-1; $i > -1 ; $i--) { 
            if ($res[$i] < $fractionSizes[$i]) {
                // everything is fine.
                break;
            }

            // A rounding got us over limit
            if ($i) {
                $res[$i] -= $fractionSizes[$i];
                $res[$i-1]++;
            }
        }

        // Possible issue : the heaviest time component could have reached it's
        // upper limit, reaching the next day. It could cause an issue depending
        // on how the time is set in the final object.
        // 
        // Usually this issue will only happen if reconverting a Republican date
        // into a conventional Date, and native implementations handle it well.

        return $res;
    }
}