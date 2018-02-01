<?php

require './vendor/autoload.php';

use Popy\RepublicanCalendar\Formater;
use Popy\Calendar\Calendar\GregorianCalendar;
use Popy\RepublicanCalendar\Converter\TimeConverter;

$format = 'l jS F y H:i:s, D|F, D, y H:i:s';
//$format = 'Y-m-d H:i:s D';

$converter = new TimeConverter();
$romme = new \Popy\Calendar\PresetFormater(new Formater(), $format);
$gregorian = new \Popy\Calendar\PresetFormater(new GregorianCalendar(), 'Y-m-d H:i:s');

$dates = [
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
    $date2 = $converter->fromRepublican($converter->toRepublican($date));
    echo $gregorian->format($date) . ' <=> ' . $gregorian->format($date2) . ' -> ' . $romme->format($date) . chr(10);
}
