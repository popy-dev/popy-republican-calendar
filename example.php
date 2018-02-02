<?php

require './vendor/autoload.php';

use Popy\Calendar\PresetFormater;
use Popy\RepublicanCalendar\Formater;
use Popy\Calendar\Calendar\GregorianCalendar;

$format = 'l jS F y H:i:s, D|F, D, y H:i:s';


$revolutionnary = new PresetFormater(new Formater($converter), $format);
$gregorian      = new PresetFormater(new GregorianCalendar(), 'Y-m-d H:i:s');

$dates = [
    // Year 1
    new DateTime('1792-09-22'),

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
