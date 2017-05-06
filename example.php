<?php

require 'quickstart.php';

$preset = new \Popy\RepublicanCalendar\PresetFormater($formater, 'l jS F Y, D', 'F, D, Y');

echo $preset->format($converter->toRepublican(new DateTime('2016-09-21'))) . chr(10);
echo $preset->format($converter->toRepublican(new DateTime('2017-09-21'))) . chr(10);
echo $preset->format($converter->toRepublican(new DateTime())) . chr(10);
