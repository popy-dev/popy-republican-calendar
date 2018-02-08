<?php

require './vendor/autoload.php';

use Popy\Calendar\PresetFormater;
use Popy\Calendar\Calendar\GregorianCalendar;
use Popy\RepublicanCalendar\Factory\CalendarFactory;

$factory = new CalendarFactory();
$republicanCalendar = $factory->buildRepublican();


$format = 'l jS F Y H:i:s, X|F, X, Y H:i:s';
//$format = 'o W N/l H:i:s';

$revolutionnary = new PresetFormater($republicanCalendar, $format);
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
    $p = $republicanCalendar->parse($f, $format);
    echo $gregorian->format($date) . ' -> ' . $gregorian->format($p) . ' -> ' . str_pad($f, 80, ' ', STR_PAD_LEFT) .chr(10);
}

$egyptian = new PresetFormater($factory->buildEgyptian(), $format);

foreach ($dates as $date) {
    echo $gregorian->format($date) . ' -> ' . $egyptian->format($date) . chr(10);
}
