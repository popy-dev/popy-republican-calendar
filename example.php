<?php

require './vendor/autoload.php';

use Popy\RepublicanCalendar\Formater;
use Popy\Calendar\Calendar\GregorianCalendar;
use Popy\RepublicanCalendar\Converter;

$format = 'l jS F y, D|F, D, y';
$format = 'l jS F Y, D|F, D, y';

$basic = new \Popy\Calendar\PresetFormater(new Formater(), $format);
$romme = new \Popy\Calendar\PresetFormater(new Formater(null, $converter = new Converter\Romme()), $format);
$gregorian = new \Popy\Calendar\PresetFormater(new GregorianCalendar(), 'Y-m-d');

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
    echo $gregorian->format($date) . ' -> ' . str_pad($basic->format($date), 60, ' ') . ' ..vs.. ' . $romme->format($date) . chr(10);
}

echo '---'.chr(10).chr(10);

$gregorian = new \Popy\Calendar\PresetFormater(new GregorianCalendar(), 'Y-m-d H:i:s');

foreach ($dates as $date) {
    echo $gregorian->format($date) . ' -> ' . $gregorian->format($converter->fromRepublican($converter->toRepublican($date))) . chr(10);
}
