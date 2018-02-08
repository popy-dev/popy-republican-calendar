<?php

namespace Popy\RepublicanCalendar\Factory;

use Popy\Calendar\Factory\ConfigurableFactory;

use Popy\RepublicanCalendar\Formater\Localisation;
use Popy\RepublicanCalendar\Converter\LeapYearCalculator;

use Popy\Calendar\Formater\SymbolFormater;
use Popy\RepublicanCalendar\Formater\ExtendedAgnosticFormater;
use Popy\RepublicanCalendar\Formater\SymbolFormater\ExtendedStandardDateSolar;

use Popy\Calendar\Parser\SymbolParser;
use Popy\RepublicanCalendar\Parser\SymbolParser\PregExtendedNative;

class CalendarFactory extends ConfigurableFactory
{
    /**
     * Available values for option "locale".
     *
     * @var array<string>
     */
    protected static $locale = [
        'republican_french' => Localisation\RepublicanHardcodedFrench::class,
        'egyptian_egyptian' => Localisation\EgyptianHardcodedEgyptian::class,
    ];

    /**
     * Available values for option "leap_wrapper".
     *
     * @var array<string>
     */
    protected static $leapWrappers = [
        'none'  => false,
        'fixed' => LeapYearCalculator\RommeWithFixedLeapDay::class,
        'odd'   => LeapYearCalculator\RommeOddLeapDay::class,
    ];

    public function buildRepublican(array $options = array())
    {
        /**
         * Year 1 timestamp.
         *
         * 1792-09-22 00:00:00 UTC
         */
        $options['era_start'] = -5594227200;
        $options['era_start_year'] = 1;
        $options['year_length'] = 365;
        $options['month'] = 'equal_length';
        $options['month_length'] = 30;
        $options['week'] = 'simple';
        $options['week_length'] = 10;

        $options += [
            'leap' => 'modern',
            'leap_wrapper' => 'odd',
            'time_ranges' => 'decimal',
            'locale' => 'republican_french',
        ];

        return $this->build($options);
    }

    public function buildEgyptian(array $options = array())
    {
        /**
         * Year 1 timestamp. (to be confirmed)
         *
         * -2781-07-19 00:00:00 UTC
         */
        $options['era_start'] = -149909875200;
        $options['era_start_year'] = 1;
        $options['year_length'] = 365;
        $options['month'] = 'equal_length';
        $options['month_length'] = 30;
        $options['week'] = 'simple';
        $options['week_length'] = 10;
        $options['leap_wrapper'] = 'none';

        $options += [
            'leap' => 'none',
            'time_ranges' => 'decimal',
            'locale' => 'egyptian_egyptian',
        ];

        return $this->build($options);
    }

    protected function buildLeapCalculator(array &$options)
    {
        $leap = parent::buildLeapCalculator($options);

        $wrapper = $this->getOptionValueChoice(
            $options,
            'leap_wrapper',
            static::$leapWrappers,
            'none'
        );

        if ($wrapper === false) {
            return $leap;
        }

        if (is_object($wrapper)) {
            return $wrapper;
        }

        return new $wrapper($leap);
    }

    protected function buildSymbolFormater(array &$options)
    {
        $internal = parent::buildSymbolFormater($options);

        return new SymbolFormater\Chain([
            new ExtendedStandardDateSolar($options['locale']),
            $internal,
        ]);
    }

    protected function buildFormater(array &$options)
    {
        return new ExtendedAgnosticFormater(
            $options['lexer'],
            $options['converter'],
            $options['symbol_formater']
        );
    }

    protected function buildSymbolParser(array &$options)
    {
        return new SymbolParser\Chain([
            new PregExtendedNative($options['locale']),
            parent::buildSymbolParser($options),
        ]);
    }
}
