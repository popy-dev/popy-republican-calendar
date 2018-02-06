<?php

namespace Popy\RepublicanCalendar;

use DateTimeZone;
use Popy\Calendar\ValueObject\Time;
use Popy\Calendar\ValueObject\TimeOffset;
use Popy\Calendar\ParserInterface;
use Popy\Calendar\ConverterInterface;
use Popy\Calendar\Parser\DateLexerResult;
use Popy\Calendar\Parser\FormatParserInterface;
use Popy\RepublicanCalendar\Converter\RepublicanPivotalDate;
use Popy\RepublicanCalendar\ValueObject\DateRepresentation\EgyptianDateTime;
use Popy\Calendar\Parser\FormatParser\PregExtendedNative as PregExtendedNativeFormatParser;
use Popy\RepublicanCalendar\Parser\SymbolParser\PregExtendedNative as PregExtendedNativeSymbolParser;

/**
 * Near-generic Egyptian & derived calendars parser. Based on generic parsers &
 * converters, with some assumptions :
 *  - 'y' symbol lexer will resolve the correct year
 *  - monthes are all 30 days long (after all, that's an Egyptian calendar)
 *  - the last month is the one having less than 30 days (only used as a trick
 *      to determine month when only 't' symbol is available)
 *  - no am/pm bullshit for now (have to be handled by Time & co)
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
        $date = $this->parseToEgyptian($input, $format, $timezone);

        return $date ? $this->converter->toDateTimeInterface($date) : null;
    }

    /**
     * Parses a date string into an EgyptainDate representation.
     * 
     * @param string            $input
     * @param string            $format
     * @param DateTimeZone|null $timezone
     * 
     * @return EgyptianDateTime
     */
    public function parseToEgyptian($input, $format, DateTimeZone $timezone = null)
    {
        if (null === $lexer = $this->formatParser->parseFormat($format)) {
            return null;
        }

        if (null === $parts = $lexer->tokenizeDate($input)) {
            return null;
        }

        $offset = $this->determineOffset($parts);

        return $res
            // SI Units
            // U   Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
            // u   Microseconds
            ->withUnixTime($parts->get('U'))
            ->withUnixMicroTime($parts->get('u'))

            // Year & index
            ->withYear($this->determineYear($parts), $parts->get('L'))
            ->withDayIndex($this->determineDayIndex($parts), null)

            // Offset & timezone
            ->withOffset($offset = $this->determineOffset($parts))
            ->withTimezone($this->determineTimezone($parts, $offset, $timezone))

            // Time
            ->withTime($this->determineTime($parts))
        ;
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
        // g & h are assumed non-implemented for now
        // g   12-hour format of an hour without leading zeros 1 through 12
        // h   12-hour format of an hour with leading zeros    01 through 12
        
        // G   24-hour format of an hour without leading zeros 0 through 23
        // H   24-hour format of an hour with leading zeros    00 through 23
        // i   Minutes with leading zeros  00 to 59
        // s   Seconds, with leading zeros 00 through 59
        // v   Milliseconds
        // μ   Microseconds (the u microseconds is used for SI microseconds)
        $time = new Time([
            $parts->getFirst('g', 'G', 'h', 'H'),
            $parts->get('i'),
            $parts->get('s'),
            $parts->get('v'),
            $parts->get('μ'),
        ]);

        // B   Swatch Internet time    000 through 999
        if (null !== $b = $parts->get('B')) {
            $time = $time->withRatio((int)$b * 1000);
        }

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
    protected function determineTimezone(DateLexerResult $parts, TimeOffset $offset, DateTimeZone $inputTz = null)
    {
        // e   Timezone identifier (added in PHP 5.1.0)    Examples: UTC, GMT, Atlantic/Azores
        // T   Timezone abbreviation   Examples: EST, MDT ...
        // O   Difference to Greenwich time (GMT) in hours Example: +0200
        // P   Difference to Greenwich time (GMT) with colon between hours and minutes (added in PHP 5.1.3)    Example: +02:00
        if (null !== $tz = $parts->getFirst('e', 'T', 'O', 'P')) {
            return new DateTimeZone($tz);
        }

        // Create a fixed timezone matching the offset.
        if (null !== $tz = $offset->buildTimeZone()) {
            return $tz;
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
        if (null !== $value = $parts->get('Z')) {
            $value = (int)$value;
        } elseif (null !== $o = $parts->getFirst('O', 'P')) {
            // O   Difference to Greenwich time (GMT) in hours Example: +0200
            // P   Difference to Greenwich time (GMT) with colon between hours and minutes (added in PHP 5.1.3)    Example: +02:00
            $o = str_replace(':', '', $o);
            $sign = substr($o, 0, 1);
            $hours = (int)substr($o, 1, 2);
            $minutes = (int)substr($o, 3, 2);

            $o = $hours * 60 + $minutes;
            $o = $o * 60;

            if ($sign === '-') {
                $o = -$o;
            }

            $value = $o;
        }

        return new TimeOffset(
            $value,
            $parts->get('I'),
            $parts->get('T')
        );
    }
}
