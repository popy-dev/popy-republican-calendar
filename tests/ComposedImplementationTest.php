<?php

namespace Popy\RepublicanCalendar\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Popy\RepublicanCalendar\Factory\CalendarFactory;

class ComposedImplementationTest extends TestCase
{
    protected $factory;
    protected $calendar;

    public function setUp() {
        $this->factory = new CalendarFactory();
        $this->calendar = $this->factory->buildRepublican();
    }

    public function provideFormattingTests()
    {
        // Year 1 / era start
        yield ['1792-09-22 00:00:00 UTC', 'Y-m-d H:i:s', '0001-01-01 00:00:00'];

        // Day index test
        yield ['1793-09-21 00:00:00 UTC', 'Y z', '0001 364'];
        yield ['1794-09-21 00:00:00 UTC', 'Y z', '0002 364'];

        // Leap years
        yield ['1792-09-22 00:00:00 UTC', 'Y-m-d H:i:s L', '0001-01-01 00:00:00 0'];
        yield ['1794-09-22 00:00:00 UTC', 'Y-m-d H:i:s L', '0003-01-01 00:00:00 1'];

        // Leap years & day count
        yield ['1793-09-17 00:00:00 UTC', 'Y-m-d H:i:s L t', '0001-13-01 00:00:00 0 5'];
        yield ['1795-09-17 00:00:00 UTC', 'Y-m-d H:i:s L t', '0003-13-01 00:00:00 1 6'];

        // Testing format alternatives
        yield ['1792-09-22 00:00:00 UTC', 'Y-m-d|Y z', '0001-01-01'];
        yield ['1793-09-17 00:00:00 UTC', 'Y-m-d|Y z', '0001 360'];

        // Decimal date format
        yield ['1792-09-22 12:00:00 UTC', 'Y-m-d H:i:s', '0001-01-01 05:00:00'];
    }

    /**
     * @dataProvider provideFormattingTests
     */
    public function testFormat($date, $format, $formatted)
    {
        $date = new DateTimeImmutable($date);

        $res = $this->calendar->format($date, $format);

        $this->assertSame($formatted, $res);
    }

    /**
     * @dataProvider provideFormattingTests
     */
    public function testParse($date, $format, $formatted, $skip = false)
    {
        if ($skip) {
            return;
        }

        $date = new DateTimeImmutable($date);
        $res = $this->calendar->parse($formatted, $format);

        if ($res) {
            $res = $res->getTimestamp();
        }

        // COmpare timestamps, as timezone can be lost.
        $this->assertSame($date->getTimestamp(), $res);
    }
}
