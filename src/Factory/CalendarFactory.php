<?php

namespace Popy\RepublicanCalendar\Factory;

use Popy\Calendar\Parser\SymbolParser;
use Popy\Calendar\Formatter\SymbolFormatter;
use Popy\Calendar\Factory\ConfigurableFactory;
use Popy\RepublicanCalendar\Formatter\Localisation;
use Popy\RepublicanCalendar\Converter\LeapYearCalculator;
use Popy\RepublicanCalendar\Formatter\ExtendedAgnosticFormatter;
use Popy\RepublicanCalendar\Parser\SymbolParser\PregExtendedNative;
use Popy\RepublicanCalendar\Formatter\SymbolFormatter\ExtendedStandardDateSolar;

class CalendarFactory extends ConfigurableFactory
{
    /**
     * Available values for option "leap_wrapper".
     *
     * @var array<string, mixed>
     */
    protected $leapWrappers = [
        'none'  => false,
        'fixed' => LeapYearCalculator\RommeWithFixedLeapDay::class,
        'odd'   => LeapYearCalculator\RommeOddLeapDay::class,
    ];

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->locale += [
            'republican_french' => Localisation\RepublicanHardcodedFrench::class,
            'egyptian_egyptian' => Localisation\EgyptianHardcodedEgyptian::class,
        ];
    }

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
            'number' => 'roman',
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

    protected function getLeap(array &$options)
    {
        $leap = parent::getLeap($options);

        $wrapper = $this->getOptionValueChoice(
            $options,
            'leap_wrapper',
            $this->leapWrappers,
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

    protected function getSymbolFormatter(array &$options)
    {
        $internal = parent::getSymbolFormatter($options);

        return new SymbolFormatter\Chain([
            new ExtendedStandardDateSolar($this->get('locale', $options), $this->get('number_converter', $options)),
            $internal,
        ]);
    }

    protected function getFormatter(array &$options)
    {
        return new ExtendedAgnosticFormatter(
            $this->get('lexer', $options),
            $this->get('converter', $options),
            $this->get('symbol_formatter', $options)
        );
    }

    protected function getSymbolParser(array &$options)
    {
        return new SymbolParser\Chain([
            new PregExtendedNative($this->get('locale', $options), $this->get('number_converter', $options)),
            parent::getSymbolParser($options),
        ]);
    }
}
