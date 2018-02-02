<?php

namespace Popy\RepublicanCalendar;

use Popy\RepublicanCalendar\Locale\HardcodedFrench as DefaultLocale;

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
        $this->locale = $locale ?: new DefaultLocale();
        $this->converter = $converter ?: new RomanConverter();
    }

    /**
     * Get a date-format symbol formatted result.
     *
     * @param RepublicanDateTime $input    Input date.
     * @param string             $symbol   Symbol.
     * @param Formater           $formater Formater/Lexer
     * 
     * Supported symbols : same as date() with :
     *  - D symbol added to match day individual name
     *
     * @return string|null
     */
    public function format(RepublicanDateTime $input, $symbol, Formater $formater)
    {
        if ($symbol === 'y') {
            return $this->converter->decimalToRoman($input->getYear());
        }

        if ($symbol === 'Y') {
            return $input->getYear();
        }

        if ($symbol === 'L') {
            // Is leap year
            return $input->isLeap();
        }

        if ($symbol === 'F') {
            // Month name
            return $this->locale->getMonthName($input->getMonth());
        }

        if ($symbol === 'm') {
            // Mois avec les zéros initiaux
            return substr('0' . $input->getMonth(), -2);
        }

        if ($symbol === 'n') {
            // Mois sans les zéros initiaux
            return $input->getMonth();
        }

        if ($symbol === 't') {
            // Nombre de jours dans le mois
            if ($input->getMonth() < 13) {
                return 30;
            }
            
            return $input->isLeap() ? 6 : 5;
        }

        if ($symbol === 'd') {
            // Jour du mois, sur deux chiffres (avec un zéro initial)
            return substr('0' . $input->getDay(), -2);
        }

        if ($symbol === 'j') {
            // Jour du mois sans les zéros initiaux
            return $input->getDay();
        }

        if ($symbol === 'l') {
            // Jour de la semaine, textuel, version longue
            return $this->locale->getWeekDayName($this->format($input, 'w', $formater));
        }

        if ($symbol === 'D') {
            // Jour de la semaine, textuel, version longue
            return $this->locale->getDayName($input->getDayIndex());
        }

        if ($symbol === 'S') {
            // Suffixe ordinal d'un nombre pour le jour du mois, en anglais, sur deux lettres
            return $this->locale->getNumberOrdinalSuffix($input->getDay());
        }

        if ($symbol === 'w') {
            // Jour de la semaine au format numérique
            return ($input->getDay() - 1) % 10;
        }

        if ($symbol === 'z') {
            // Jour de l'année
            return $input->getDayIndex();
        }

        if ($symbol === 'N') {
            // Représentation numérique ISO-8601 du jour de la semaine (ajouté en PHP 5.1.0)    
            return $this->format($input, 'w', $formater) + 1;
        }

        if ($symbol === 'W') {
            // Numéro de semaine dans l'année
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
            return substr('0' . $input->getHours(), -2);
        }

        if ($symbol === 'i') {
            // i   Minutes with leading zeros  00 to 59
            return substr('0' . $input->getMinutes(), -2);
        }

        if ($symbol === 's') {
            // s   Seconds, with leading zeros 00 through 59
            return substr('0' . $input->getSeconds(), -2);
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
            return '{NIY}';
        }

        if ($symbol === 'I') {
            // I (capital i)   Whether or not the date is in daylight saving time  1 if Daylight Saving Time, 0 otherwise.
            return '{NIY}';
        }

        if ($symbol === 'O') {
            // O   Difference to Greenwich time (GMT) in hours Example: +0200
            return '{NIY}';
        }

        if ($symbol === 'P') {
            // P   Difference to Greenwich time (GMT) with colon between hours and minutes (added in PHP 5.1.3)    Example: +02:00
            return '{NIY}';
        }

        if ($symbol === 'T') {
            // T   Timezone abbreviation   Examples: EST, MDT ...
            return '{NIY}';
        }

        if ($symbol === 'Z') {
            // Z   Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive.  -43200 through 50400
            return '{NIY}';
        }

        if ($symbol === 'c') {
            // c   ISO 8601 date (added in PHP 5)  2004-02-12T15:19:21+00:00
            // Would require getting back to the Formater/Lexer with 'Y-m-d\TH:i:sP'
            return $formater->formatRepublican($input, 'Y-m-d\TH:i:sP');
        }

        if ($symbol === 'r') {
            // r   » RFC 2822 formatted date   Example: Thu, 21 Dec 2000 16:01:07 +0200
            // Would require getting back to the Formater/Lexer with 'D, d M Y H:i:s P'
            return $formater->formatRepublican($input, 'D, d M Y H:i:s P');
        }

        if ($symbol === 'U') {
            // U   Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)  See also time()
            return '{NIY}';
        }

        return $symbol;
    }
}
