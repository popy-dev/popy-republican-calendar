<?php

require 'quickstart.php';

$gregorian = new \Popy\Calendar\Calendar\GregorianCalendar();

$preset = new \Popy\Calendar\PresetFormater($formater, 'l jS F y, D|F, D, y');
$greg = new \Popy\Calendar\PresetFormater($gregorian, 'Y-m-d');

$dates = [
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
    echo $greg->format($date) . ' -> ' . $preset->format($date) . chr(10);
}
