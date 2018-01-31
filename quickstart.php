<?php

require './vendor/autoload.php';

$formater = new \Popy\RepublicanCalendar\Formater\RepublicanCalendar(
    new \Popy\RepublicanCalendar\SymbolFormater(
        new \Popy\RepublicanCalendar\Locale\HardcodedFrench(),
        new \Popy\RepublicanCalendar\RomanConverter()
    ),
    new \Popy\RepublicanCalendar\Converter\Basic()
);

$gregorian = new \Popy\RepublicanCalendar\Formater\GregorianCalendar();
