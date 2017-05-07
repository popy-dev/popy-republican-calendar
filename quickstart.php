<?php

require './vendor/autoload.php';

$formater = new \Popy\RepublicanCalendar\Formater(
    new \Popy\RepublicanCalendar\SymbolFormater(
        new \Popy\RepublicanCalendar\Locale\HardcodedFrench(),
        new \Popy\RepublicanCalendar\RomanConverter()
    )
);

$converter = new \Popy\RepublicanCalendar\Converter\Basic();
