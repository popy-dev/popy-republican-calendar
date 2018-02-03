<?php

namespace Popy\RepublicanCalendar\Formater;

use Popy\RepublicanCalendar\FormaterInterface;
use Popy\Calendar\Formater\LocaleInterface;
use Popy\Calendar\Formater\Utility\RomanConverter;
use Popy\RepublicanCalendar\Formater\Localisation\RepublicanHardcodedFrench;
use Popy\RepublicanCalendar\Converter\DateTimeRepresentation\EgyptianDateTime;

class SymbolFormater
{
    /**
     * Locale (used for day & month names)
     *
     * @var LocaleInterface
     */
    protected $locale;

    /**
     * Roman number convertor.
     *
     * @var RomanConverter
     */
    protected $converter;

    /**
     * Class constructor.
     *
     * @param LocaleInterface|null $locale
     * @param RomanConverter|null  $converter
     */
    public function __construct(LocaleInterface $locale = null, RomanConverter $converter = null)
    {
        $this->locale = $locale ?: new RepublicanHardcodedFrench();
        $this->converter = $converter ?: new RomanConverter();
    }

    /**
     * Get a date-format symbol formatted result.
     *
     * @param EgyptianDateTime  $input    Input date.
     * @param string            $symbol   Symbol.
     * @param FormaterInterface $formater Formater/Lexer
     *
     * Supported symbols : same as date() with :
     *  - X symbol added to match day individual name
     *  - y symbol returns a roman representation of the year, as a 2 digits
     *      year representation doesn't make sense
     *
     * @return string|null
     */
    public function format(EgyptianDateTime $input, $symbol, FormaterInterface $formater)
    {
        if ($symbol === 'y') {
            // y   A two digit representation of a year
            return $this->converter->decimalToRoman($input->getYear());
        }

        if ($symbol === 'Y' || $symbol === 'o') {
            // Y   A full numeric representation of a year, 4 digits
            // o   ISO-8601 week-numbering year. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.
            return sprintf('%04d', $input->getYear());
        }

        if ($symbol === 'L') {
            // L   Whether it's a leap year
            return $input->isLeapYear();
        }

        if ($symbol === 'F') {
            // F   A full textual representation of a month, such as January or March
            return $this->locale->getMonthName($input->getMonth());
        }

        if ($symbol === 'M') {
            // M   A short textual representation of a month, three letters    Jan through Dec
            return $this->locale->getMonthShortName($input->getMonth());
        }

        if ($symbol === 'm') {
            // m   Numeric representation of a month, with leading zeros   01 through 12
            return sprintf('%02d', $input->getMonth());
        }

        if ($symbol === 'n') {
            // n   Numeric representation of a month, without leading zeros
            return $input->getMonth();
        }

        if ($symbol === 't') {
            // t   Number of days in the given month
            if ($input->getMonth() < 13) {
                return 30;
            }
            
            return $input->isLeapYear() ? 6 : 5;
        }

        if ($symbol === 'd') {
            // d   Day of the month, 2 digits with leading zeros   01 to 31
            return sprintf('%02d', $input->getDay());
        }

        if ($symbol === 'j') {
            // j   Day of the month without leading zeros  1 to 31
            return $input->getDay();
        }

        if ($symbol === 'l') {
            // l (lowercase 'L')   A full textual representation of the day of the week
            return $this->locale->getDayName('w' . $this->format($input, 'w', $formater));
        }

        if ($symbol === 'D') {
            // D   A textual representation of a day, three letters
            return $this->locale->getDayShortName('w' . $this->format($input, 'w', $formater));
        }

        if ($symbol === 'X') {
            // Added symbol : Day individual name
            return $this->locale->getDayName('y' . $input->getDayIndex());
        }

        if ($symbol === 'S') {
            // S   English ordinal suffix for the day of the month, 2 characters
            return $this->locale->getNumberOrdinalSuffix($input->getDay());
        }

        if ($symbol === 'w') {
            // w   Numeric representation of the day of the week   0 (for Sunday) through 6 (for Saturday)
            return ($input->getDay() - 1) % 10;
        }

        if ($symbol === 'z') {
            // z   The day of the year (starting from 0)
            return $input->getDayIndex();
        }

        if ($symbol === 'N') {
            // N   ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0) 1 (for Monday) through 7 (for Sunday)
            return $this->format($input, 'w', $formater) + 1;
        }

        if ($symbol === 'W') {
            // W   ISO-8601 week number of year, weeks starting on Monday
            return $this->format($input, 'z', $formater) % 10;
        }

        if ($symbol === 'a' || $symbol === 'A') {
            // a   Lowercase Ante meridiem and Post meridiem   am or pm
            // A   Uppercase Ante meridiem and Post meridiem   AM or PM
            return '';
        }

        if ($symbol === 'B') {
            // B   Swatch Internet time    000 through 999
            return $input->getHours() * 100 + $input->getMinutes();
        }

        if ($symbol === 'g' || $symbol === 'G') {
            // g   12-hour format of an hour without leading zeros 1 through 12
            // G   24-hour format of an hour without leading zeros 0 through 23
            return $input->getHours();
        }

        if ($symbol === 'h' || $symbol === 'H') {
            // h   12-hour format of an hour with leading zeros    01 through 12
            // H   24-hour format of an hour with leading zeros    00 through 23
            return sprintf('%02d', $input->getHours());
        }

        if ($symbol === 'i') {
            // i   Minutes with leading zeros  00 to 59
            return sprintf('%02d', $input->getMinutes());
        }

        if ($symbol === 's') {
            // s   Seconds, with leading zeros 00 through 59
            return sprintf('%02d', $input->getSeconds());
        }

        if ($symbol === 'u') {
            // u   Microseconds
            return str_pad($input->getMicroseconds(), 6, '0', STR_PAD_LEFT);
        }

        if ($symbol === 'v') {
            // u   Milliseconds
            return substr($this->format($input, 'u', $formater), 0, 3);
        }

        if ($symbol === 'e') {
            // e   Timezone identifier (added in PHP 5.1.0)    Examples: UTC, GMT, Atlantic/Azores
            return $input->getTimezone()->getName();
        }

        if ($symbol === 'I') {
            // I (capital i)   Whether or not the date is in daylight saving time  1 if Daylight Saving Time, 0 otherwise.
            return '{NIY}';
        }

        if ($symbol === 'O') {
            // O   Difference to Greenwich time (GMT) in hours Example: +0200
            return sprintf(
                '%s%02d%02d',
                $input->getOffset() < 0 ? '-' : '+',
                intval(abs($input->getOffset()) / 3600),
                intval(abs($input->getOffset())%60 / 60)
            );
        }

        if ($symbol === 'P') {
            // P   Difference to Greenwich time (GMT) with colon between hours and minutes (added in PHP 5.1.3)    Example: +02:00
            // O   Difference to Greenwich time (GMT) in hours Example: +0200
            return sprintf(
                '%s%02d:%02d',
                $input->getOffset() < 0 ? '-' : '+',
                intval(abs($input->getOffset()) / 3600),
                intval(abs($input->getOffset())%60 / 60)
            );
        }

        if ($symbol === 'T') {
            // T   Timezone abbreviation   Examples: EST, MDT ...
            return '{NIY}';
        }

        if ($symbol === 'Z') {
            // Z   Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive.  -43200 through 50400
            return $input->getOffset();
        }

        if ($symbol === 'c') {
            // c   ISO 8601 date (added in PHP 5)  2004-02-12T15:19:21+00:00
            // Would require getting back to the Formater/Lexer with 'Y-m-d\TH:i:sP'
            return $formater->formatEgyptian($input, 'Y-m-d\TH:i:sP');
        }

        if ($symbol === 'r') {
            // r   » RFC 2822 formatted date   Example: Thu, 21 Dec 2000 16:01:07 +0200
            // Would require getting back to the Formater/Lexer with 'D, d M Y H:i:s P'
            return $formater->formatEgyptian($input, 'D, d M Y H:i:s P');
        }

        if ($symbol === 'U') {
            // U   Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)  See also time()
            return $input->getTimestamp();
        }

        return $symbol;
    }
}
