<?php

require 'quickstart.php';

$preset = new \Popy\RepublicanCalendar\PresetFormater($formater, 'l jS F y, D', 'F, D, y');

// Sans-culottide day (actually the revolution day, as it's a leap day)
echo $preset->format($converter->toRepublican(new DateTime('2016-09-21'))) . chr(10);

// First day of year 225
echo $preset->format($converter->toRepublican(new DateTime('2016-09-22'))) . chr(10);

// Last day of year 225
echo $preset->format($converter->toRepublican(new DateTime('2017-09-21'))) . chr(10);

// Today
echo $preset->format($converter->toRepublican(new DateTime())) . chr(10);
