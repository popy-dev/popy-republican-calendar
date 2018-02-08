<?php

require './vendor/autoload.php';

use Popy\Calendar\Calendar\ComposedCalendar;
use Popy\Calendar\Converter\AgnosticConverter;
use Popy\Calendar\Converter\UnixTimeConverter;
use Popy\Calendar\Converter\LeapYearCalculator;
use Popy\RepublicanCalendar\Formater\Localisation;
use Popy\RepublicanCalendar\Converter\LeapYearCalculator as RCLeapYear;

use Popy\Calendar\Formater\SymbolFormater;
use Popy\RepublicanCalendar\Formater\ExtendedAgnosticFormater;
use Popy\RepublicanCalendar\Formater\SymbolFormater\ExtendedStandardDateSolar;

use Popy\Calendar\Parser\AgnosticParser;
use Popy\Calendar\Parser\ResultMapper;
use Popy\Calendar\Parser\FormatLexer;
use Popy\Calendar\Parser\FormatParser;
use Popy\Calendar\Parser\SymbolParser;
use Popy\RepublicanCalendar\Parser\SymbolParser\PregExtendedNative;

$calc = new RCLeapYear\RommeWithFixedLeapDay(
    new LeapYearCalculator\Modern(365, 1)
);
$locale = new Localisation\RepublicanHardcodedFrench();

$converter = new AgnosticConverter(new UnixTimeConverter\Chain([
    new UnixTimeConverter\StandardDateFactory(),
    new UnixTimeConverter\Date(),
    new UnixTimeConverter\TimeOffset(),
    /**
     * Year 1 timestamp.
     *
     * 1792-09-22 00:00:00 UTC
     */
    new UnixTimeConverter\DateSolar($calc, -5594227200),
    new UnixTimeConverter\EqualLengthMonthes($calc, 30),
    new UnixTimeConverter\SimpleWeeks(10),
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

$formater = new ExtendedAgnosticFormater(
    new FormatLexer\MbString(),
    $converter,
    $symbolFormater
);

$mappers = new ResultMapper\Chain([
    new ResultMapper\StandardDateFactory(),
    new ResultMapper\StandardDate(),
    new ResultMapper\StandardDateFragmented(),
    new ResultMapper\StandardDateSolar(),
    new ResultMapper\StandardDateTime(),
]);

$parser = new AgnosticParser(
    new FormatParser\PregExtendedNative(
        null,
        new SymbolParser\Chain([
            new PregExtendedNative($locale),
            new SymbolParser\PregNative($locale),
        ])
    ),
    $mappers,
    $converter
);

$calendar = new ComposedCalendar($formater, $parser);

use Popy\Calendar\PresetFormater;
use Popy\Calendar\Calendar\GregorianCalendar;

$format = 'l jS F Y H:i:s, X|F, X, Y H:i:s';
//$format = 'o W N/l H:i:s';

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
    $f = $revolutionnary->format($date);
    $p = $calendar->parse($f, $format);
    echo $gregorian->format($date) . ' -> ' . $gregorian->format($p) . ' -> ' . str_pad($f, 80, ' ', STR_PAD_LEFT) .chr(10);
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
