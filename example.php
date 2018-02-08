<?php

require './vendor/autoload.php';

use Popy\Calendar\Calendar\ComposedCalendar;
use Popy\Calendar\Converter\AgnosticConverter;
use Popy\Calendar\Converter\UnixTimeConverter;
use Popy\Calendar\Converter\LeapYearCalculator;
use Popy\RepublicanCalendar\Formater\Localisation;
use Popy\RepublicanCalendar\Converter\LeapYearCalculator as RCLeapYear;
use Popy\RepublicanCalendar\Converter\UnixTimeConverter as RCConverter;

use Popy\Calendar\Formater\SymbolFormater;
use Popy\Calendar\Formater\AgnosticFormater;
use Popy\RepublicanCalendar\Formater\SymbolFormater\ExtendedStandardDateSolar;

use Popy\Calendar\Parser\AgnosticParser;
use Popy\Calendar\Parser\ResultMapper;
use Popy\Calendar\Parser\FormatLexer;
use Popy\Calendar\Parser\FormatParser\PregExtendedNative;

$calc = new RCLeapYear\RommeWithFixedLeapDay(
    new LeapYearCalculator\Modern(365, 1)
);
$locale = new Localisation\RepublicanHardcodedFrench();

$converter = new AgnosticConverter(new UnixTimeConverter\Chain([
    new UnixTimeConverter\GregorianDateFactory(),
    new UnixTimeConverter\Date(),
    new UnixTimeConverter\TimeOffset(),
    /**
     * Year 1 timestamp.
     *
     * 1792-09-22 00:00:00 UTC
     */
    new UnixTimeConverter\DateSolar($calc, -5594227200),
    new RCConverter\TriDecimalMonthes($calc),
    new RCConverter\DecimalWeeks(),
    new UnixTimeConverter\Time([10, 100, 100, 1000, 1000]),
]));

$symbolFormater = new SymbolFormater\Chain([
    new SymbolFormater\Litteral(),
    new ExtendedStandardDateSolar($locale),
    new SymbolFormater\StandardDate(),
    new SymbolFormater\StandardDateFragmented($locale),
    new SymbolFormater\StandardDateSolar(),
    new SymbolFormater\StandardDateTime(),
    new SymbolFormater\StandardRecursive(),
    new SymbolFormater\Litteral(true),
]);

$formater = new AgnosticFormater(
    new FormatLexer\MbString(),
    $converter,
    $symbolFormater
);


use Popy\Calendar\PresetFormater;
use Popy\Calendar\Calendar\GregorianCalendar;



$format = 'Y-m-d H:i:s l jS F Y H:i:s, X|F, X, y H:i:s';

$revolutionnary = new PresetFormater($formater, $format);
$gregorian      = new PresetFormater(new GregorianCalendar(), 'Y-m-d H:i:s');

$dates = [
    //new DateTime('900-01-01'),
    // Year 1
    new DateTime('1792-09-22 00:00:00'),

    new DateTime('1811-09-23'),
    // Sans-culottide day (actually the revolution day, as it's a leap year)
    new DateTime('2016-09-21'),

    // First day of year 225
    new DateTime('2016-09-22'),

    // Last day of year 225
    new DateTime('2017-09-21'),

    // Today
    new DateTime(),
];

foreach ($dates as $date) {
    echo $gregorian->format($date) . ' -> ' . $revolutionnary->format($date) . chr(10);
}

die();

$egyptian = new PresetFormater(
    new Formater(
        new EgyptianPivotalDate(),
        new SymbolFormater(new EgyptianHardcodedEgyptian())
    ),
    'Y-m-d H:i:s / F'
);

foreach ($dates as $date) {
    echo $gregorian->format($date) . ' -> ' . $egyptian->format($date) . chr(10);
}
