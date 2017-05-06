<?php

namespace Popy\RepublicanCalendar;

class Formater
{
    /**
     * Locale (used for day & month names)
     *
     * @var LocaleInterface
     */
    protected $locale;

    /**
     * Class constructor.
     *
     * @param LocaleInterface $locale
     */
    public function __construct(LocaleInterface $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Format a date into a string.
     * @param Date        $input               Input date.
     * @param string      $format              Date format (php/date compatible, with D symbol added to match day individual name)
     * @param string|null $sansCulottideFormat Alternate format for "sans-culottides" days (filler days).
     * 
     * @return string
     */
    public function format(Date $input, $format, $sansCulottideFormat = null)
    {
        $symbols = array(
            'Y', 'L',
            'F', 'm', 'n', 't',
            'd', 'j', 'l', 'S', 'w', 'z', 'N', 'D',
            'W'
        );

        if ($sansCulottideFormat !== null && $input->getMonth() === 13) {
            $format = $sansCulottideFormat;
        }

        $string = str_split($format, 1);
        $skipNext = false;

        foreach ($string as $k => $v) {
            if ($skipNext) {
                $skipNext = false;
                continue;
            }

            if ($v === '\\') {
                $skipNext = true;
                continue;
            }

            if (!in_array($v, $symbols)) {
                continue;
            }

            $string[$k] = $this->getSymbolValue($input, $v);
        }

        return implode('', $string);
    }

    /**
     * Get a date-format symbol formatted result.
     *
     * @param Date   $input  Input date.
     * @param string $symbol Symbol.
     *
     * @return string|null
     */
    public function getSymbolValue(Date $input, $symbol)
    {
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
            
            return $this->getSymbolValue($input, 'L') ? 6 : 5;
        }

        if ($symbol === 'd') {
            // Jour du mois, sur deux chiffres (avec un zéro initial)
            return substr('0' . $input->getDay(), -2);;
        }

        if ($symbol === 'j') {
            // Jour du mois sans les zéros initiaux
            return $input->getDay();
        }

        if ($symbol === 'l') {
            // Jour de la semaine, textuel, version longue
            return $this->locale->getWeekDayName($this->getSymbolValue($input, 'w'));
        }

        if ($symbol === 'D') {
            // Jour de la semaine, textuel, version longue
            return $this->locale->getDayName($this->getSymbolValue($input, 'z'));
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
            return $input->getMonth() * 30 + $input->getDay() - 30 - 1;
        }

        if ($symbol === 'N') {
            // Représentation numérique ISO-8601 du jour de la semaine (ajouté en PHP 5.1.0)    
            return $this->getSymbolValue($input, 'w') + 1;
        }

        if ($symbol === 'W') {
            // Numéro de semaine dans l'année
            return $this->getSymbolValue($input, 'z') % 10;
        }
    }
}