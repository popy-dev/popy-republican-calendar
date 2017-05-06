<?php

require './vendor/autoload.php';

$formater = new \Popy\RepublicanCalendar\Formater(
    new \Popy\RepublicanCalendar\Locale\HardcodedFrench()
);

$converter = new \Popy\RepublicanCalendar\Converter\Basic();
