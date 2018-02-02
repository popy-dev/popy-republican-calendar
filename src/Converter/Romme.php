<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use Popy\RepublicanCalendar\RepublicanDateTime;
use Popy\RepublicanCalendar\ConverterInterface;

/**
 * Romme implementation, adapted from caarmen's implementation. Kept as tribute.
 *
 * Does not handle time (by design), so better wrap it with TimeConverter.
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

        return new RepublicanDateTime($year, $dayIndex, $leap, $input);
    }

    /**
     * {@inheritDoc}
     */
    public function fromRepublican(RepublicanDateTime $input)
    {
        $frenchEraEnd = $this->getRevolutionEraEnd($input->getTimezone());

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
            'U',
            $frenchEraEnd->getTimestamp() + $numSecondsSinceEndOfFrenchEra,
            $input->getTimezone()
        );

        return $result;
    }
}
