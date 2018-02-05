<?php

namespace Popy\RepublicanCalendar;

use DateTimeZone;
use Popy\Calendar\ParserInterface;
use Popy\Calendar\ConverterInterface;
use Popy\Calendar\Parser\DateLexerResult;
use Popy\Calendar\Parser\FormatParserInterface;
use Popy\RepublicanCalendar\Converter\RepublicanPivotalDate;
use Popy\RepublicanCalendar\Converter\DateTimeRepresentation\EgyptianDateTime;
use Popy\Calendar\Parser\FormatParser\PregExtendedNative as PregExtendedNativeFormatParser;
use Popy\RepublicanCalendar\Parser\SymbolParser\PregExtendedNative as PregExtendedNativeSymbolParser;

/**
 * Near-generic Egyptian & derived calendars parser. Based on generic parsers &
 * converters, with some assumptions :
 *  - 'y' symbol lexer will resolve the correct year
 *  - monthes are all 30 days long (after all, that's an Egyptian calendar)
 *  - the last month is the one having less than 30 days (only used as a trick
 *      to determine month when only 't' symbol is available)
 *  
 */
class Parser implements ParserInterface
{
    /**
     * Format parser.
     *
     * @var FormatParserInterface
     */
    protected $formatParser;

    /**
     * Date converter.
     *
     * @var ConverterInterface
     */
    protected $converter;

    /**
     * Class constructor.
     *
     * @param FormatParserInterface|null $formatParser Format parser.
     * @param ConverterInterface|null    $converter    Date converter.
     */
    public function __construct(FormatParserInterface $formatParser = null, ConverterInterface $converter = null)
    {
        $this->formatParser = $formatParser ?: new PregExtendedNativeFormatParser(
            null,
            new PregExtendedNativeSymbolParser()
        );

        $this->converter = $converter ?: new RepublicanPivotalDate();
    }

    /**
     * @inheritDoc
     */
    public function parse($input, $format, DateTimeZone $timezone = null)
    {
        if (null === $lexer = $this->formatParser->parseFormat($format)) {
            return null;
        }

        if (null === $parts = $lexer->tokenizeDate($input)) {
            return null;
        }

        $offset = $this->determineOffset($parts);

        $res = new EgyptianDateTime(
            $this->determineYear($parts),
            $this->determineDayIndex($parts),
            (bool)$parts->get('L'), // Not used anyway
            $this->determineTimezone($parts, $offset, $timezone),
            $offset
        );

        $res = $res->setTime($this->determineTime($parts));

        // U   Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)  See also time()
        if (null !== $tstamp = $parts->get('U')) {
            $res = $res->setTimestamp((int)$tstamp);
        }

        return $this->converter->toDateTimeInterface($res);
    }

    /**
     * Determine year.
     *
     * @param DateLexerResult $parts
     *
     * @return integer|null
     */
    protected function determineYear(DateLexerResult $parts)
    {
        return $parts->getFirst('Y', 'o', 'y');
    }

    /**
     * Determine year.
     *
     * @param DateLexerResult $parts
     *
     * @return integer|null
     */
    protected function determineDayIndex(DateLexerResult $parts)
    {
        // z   The day of the year (starting from 0)
        // X   Day individual name
        if (null !== $z = $parts->getFirst('z', 'X')) {
            return (int)$z;
        }

        // W   ISO-8601 week number of year, weeks starting on Monday
        $w = $parts->get('W');
        if (null !== $w && null !== $dow = $this->determineDayOfWeek($parts)) {
            return $parts->get('W') * 10 + $dow;
        }

        if (
            (null !== $m = $this->determineMonth($parts))
            && null !== $d = $this->determineDay($parts)
        ) {
            return $m * 30 + $d;
        }
    }

    /**
     * Determine month (0 indexed).
     *
     * @param DateLexerResult $parts
     *
     * @return integer
     */
    protected function determineMonth(DateLexerResult $parts)
    {
        // m   Numeric representation of a month, with leading zeros   01 through 12
        // n   Numeric representation of a month, without leading zeros
        // F   A full textual representation of a month, such as January or March
        // M   A short textual representation of a month, three letters
        if (null !== $m = $parts->getFirst('m', 'n', 'F', 'M')) {
            return (int)$m - 1;
        }

        // t   Number of days in the given month
        if (intval($parts->get('t', 30)) < 30) {
            return 12;
        }
    }

    /**
     * Determine day (0 indexed).
     *
     * @param DateLexerResult $parts
     * 
     * @return integer|null
     */
    protected function determineDay(DateLexerResult $parts)
    {
        // d   Day of the month, 2 digits with leading zeros   01 to 31
        // j   Day of the month without leading zeros  1 to 31
        if (null !== $d = $parts->getFirst('j', 'd')) {
            return (int)$d - 1;
        }
    }

    /**
     * Determine day of week (0 indexed)
     *
     * @param DateLexerResult $parts
     * 
     * @return integer|null
     */
    protected function determineDayOfWeek(DateLexerResult $parts)
    {
        // w   Numeric representation of the day of the week   0 (for Sunday) through 6 (for Saturday)
        // D   A textual representation of a day, three letters
        // l (lowercase 'L')   A full textual representation of the day of the week
        if (null !== $w = $parts->getFirst('w', 'D', 'l')) {
            return (int)$w;
        }

        // N   ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0) 1 (for Monday) through 7 (for Sunday)
        if (null !== $N = $parts->get('N')) {
            return (int)$N - 1;
        }
    }

    /**
     * Determine time of day.
     *
     * @param DateLexerResult $parts
     *
     * @return array<int>
     */
    protected function determineTime(DateLexerResult $parts)
    {
        // g   12-hour format of an hour without leading zeros 1 through 12
        // G   24-hour format of an hour without leading zeros 0 through 23
        // h   12-hour format of an hour with leading zeros    01 through 12
        // H   24-hour format of an hour with leading zeros    00 through 23
        // i   Minutes with leading zeros  00 to 59
        // s   Seconds, with leading zeros 00 through 59
        // u   Microseconds
        $time = [
            $parts->getFirst('g', 'G', 'h', 'H'),
            $parts->get('i'),
            $parts->get('s'),
            $parts->get('u'),
        ];

        // v   Milliseconds
        if ($time[3] === null && null !== $m = $parts->get('v')) {
            $time[3] = $m * 1000;
        }

        // B   Swatch Internet time    000 through 999
        // NIY as we can't determine the time format.
        // Will probably have to handle Time as an object.

        for ($i=3; $i > -1; $i--) {
            if ($time[$i] === null) {
                unset($time[$i]);
            } else {
                break;
            }
        }

        $time = array_map('intval', $time);

        return $time;
    }

    /**
     * Determine date's timezone. If an offset has been found, Timezone has
     * no effect on the date parsing, but will have on the date display.
     *
     * @param DateLexerResult   $parts   Date lexer results.
     * @param integer|null      $offset  Date offset if it has been found.
     * @param DateTimeZone|null $inputTz Default timezone if any.
     *
     * @return DateTimeZone
     */
    protected function determineTimezone(DateLexerResult $parts, $offset, DateTimeZone $inputTz = null)
    {
        // O & P are valid timezones constructor parameter, so use it
        if (null !== $tz = $parts->getFirst('O', 'P')) {
            return new DateTimeZone($tz);
        }

        // Create a fixed timezone matching the offset.
        if ($offset !== null) {
            $sign = $offset < 0 ? '-' : '+';

            return new DateTimeZone(sprintf(
                '%s%02d:%02d',
                $sign,
                intval(abs($offset)/3600),
                intval((abs($offset)%3600) / 60)
            ));
        }

        // e   Timezone identifier (added in PHP 5.1.0)    Examples: UTC, GMT, Atlantic/Azores
        // T   Timezone abbreviation   Examples: EST, MDT ...
        if (null !== $tz = $parts->getFirst('e', 'T')) {
            return new DateTimeZone($tz);
        }

        if (null !== $inputTz) {
            return $inputTz;
        }

        // Fallback.
        return new DateTimeZone(date_default_timezone_get());
    }

    protected function determineOffset(DateLexerResult $parts)
    {
        // Z   Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive.  -43200 through 50400
        if (null !== $o = $parts->get('Z')) {
            return (int)$o;
        }

        // O   Difference to Greenwich time (GMT) in hours Example: +0200
        // P   Difference to Greenwich time (GMT) with colon between hours and minutes (added in PHP 5.1.3)    Example: +02:00
        if (null !== $o = $parts->getFirst('O', 'P')) {
            $o = str_replace(':', '', $o);
            $sign = substr($o, 0, 1);
            $hours = (int)substr($o, 1, 2);
            $minutes = (int)substr($o, 3, 2);

            $o = $hours * 60 + $minutes;
            $o = $o * 60;

            if ($sign === '-') {
                $o = -$o;
            }

            return $o;
        }
    }
}
