<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTime;
use DateTimeInterface;
use BadMethodCallException;
use Popy\RepublicanCalendar\Date;
use Popy\RepublicanCalendar\ConverterInterface;

/**
 * Romme implementation, adapted from caarmen's implementation
 *
 * @link https://github.com/caarmen/french-revolutionary-calendar
 */
class Romme implements ConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function toRepublican(DateTimeInterface $input)
    {
        $frenchEraEnd = DateTime::createFromFormat('Y-m-d H:i:s', '1811-09-23 00:00:00', $input->getTimezone());

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
        $fakeEndFrenchEraTimestamp = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            '2020-01-01 00:00:00',
            $input->getTimezone()
        )->getTimestamp();

        // Add the elapsed time to the French date.
        $fakeFrenchTimestamp = $fakeEndFrenchEraTimestamp + $numSecondsSinceEndOfFrenchEra;

        // Create a calendar object for the French date
        $fakeFrenchDate = new DateTime('now', $input->getTimezone());
        $fakeFrenchDate->setTimeStamp($fakeFrenchTimestamp);

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
        // Create a fake calendar object (fake because this is not really a
        // Gregorian date), corresponding to the end of the French calendar era.
        // This was in the French year 20. Since in the Gregorian year 20, there
        // were no leap years yet, we add 2000 to the year, so that the
        // Gregorian calendar implementation can handle the leap years.
        $fakeEndFrenchEraEndCalendar = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            '2020-01-01 00:00:00'
            //$input->getTimezone()
        );

        // Create a fake calendar object for the given French date
        $fakeFrenchCalendar = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            sprintf(
                '%s-01-01 00:00:00',
                2000 + $input->getYear()
            )
            //$input->getTimezone()
        );

        if ($input->getDayIndex()) {
            $fakeFrenchCalendar = $fakeFrenchCalendar->modify(sprintf(
                '+ %s day',
                $input->getDayIndex()
            ));
        }

        // Determine how much time passed since the end of the French calendar (its year 20, Gregorian year 1811) and the given French date
        $numSecondsSinceEndOfFrenchEra =
            $fakeFrenchCalendar->getTimestamp() + $fakeFrenchCalendar->format('Z')
            - $fakeEndFrenchEraEndCalendar->getTimestamp() - $fakeEndFrenchEraEndCalendar->format('Z')
        ;

        $frenchEraEnd = DateTime::createFromFormat('Y-m-d H:i:s', '1811-09-23 00:00:00'/*, $input->getTimezone()*/);

        $time = $this->fromRepublicanTime($input);
        // Create the Gregorian calendar object starting with 1811 and adding this time passed
        $result = new DateTime(/*'now', $input->getTimezone()*/);
        $result = $result->setTimeStamp($frenchEraEnd->getTimestamp() + $numSecondsSinceEndOfFrenchEra);

        $result = DateTime::createFromFormat('U.u', '1811-09-23 00:00:00'/*, $input->getTimezone()*/);


        $result = $result->setTime($time[0], $time[1], $time[2], $time[3]);

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
     * @param array  $timeParts     Time constituents array.
     * @param array  $fractionSizes Time constituants ranges.
     *
     * @return float
     */
    protected function getDayFractionFromTime(array $timeParts, array $fractionSizes)
    {
        if (count($timeParts) !== count($fractionSizes)) {
            throw new BadMethodCallException('timeParts and fractionSizes arguments must have the same length !');
        }

        $fraction = 0;

        for ($i=count($timeParts) - 1; $i > -1; $i--) { 
            $fraction = ($fraction + $timeParts[$i]) / $fractionSizes[$i];
        }

        return $fraction;
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

        for ($i=0; $i < count($fractionSizes); $i++) { 
            $dayFraction = $dayFraction * $fractionSizes[$i];
            $dayFraction = $dayFraction - ($res[] = (int)$dayFraction);
        }

        var_dump('Lost : ' . $dayFraction);

        return $res;
    }
}