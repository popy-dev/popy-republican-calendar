French Republican Calendar
==========================

This repository holds a half-assed popy/calendar implementation of the French
Republican/Revolutionary calendar.

https://en.wikipedia.org/wiki/French_Republican_Calendar

Date convertors are inspired from [caarmen's work](https://github.com/caarmen/french-revolutionary-calendar).

The dates conversion usually take into account DateTimeZone, and daylight
savings offset, which are keeped as regular hour jumps, at the same dates/hours
than with regular dates, which gives constistency in a day.
But aren't daylight savings stupid anyway ?

Every class constructor is callable without arguments, and will build their
dependencies if needed.

Usage
-----

Usage is basicall the same as any popy/php-calendar implementation :

```php
<?php

use Popy\RepublicanCalendar\Formater;

$formater = new Formater();


echo $calendar->format(new DateTime(), 'Y-m-d');

?>
```

Calculation Methods
-------------------

The initial calendar definition had vague rules on where to insert leap days,
and it was abandonned before a solution has been decided (or needed). Multiple
leap days calculators are available :

- Caesar : the julian calendar one. Very imprecise.
- Modern : the system we are using for standard dates.
- Futuristic : The more precise system I found on wikipedia, which will proove
    more precise after the 4th millenium.
- RommeWithContinuedImpairLeapDay : Wrapps any calculator, but transmit a
    different year value in order to make sure that the leap years will match
    the first 20 years leaps.
- RommeWithFixedLeapDay : wrapps any calculator, will transmit a different year
    during the first 20 years (same reason than above) then will become
    transparent.

Constructors will defaults on a RommeWithContinuedImpairLeapDay/Modern couple.

```php
<?php

use Popy\RepublicanCalendar\Formater;
use Popy\RepublicanCalendar\Converter\RelativeTimestampLeapYear;
use Popy\RepublicanCalendar\LeapYearCalculator\{Futuristic,RommeWithContinuedImpairLeapDay};

// Let's do better than catholics !
$calculator = new RommeWithContinuedImpairLeapDay(
    new Futuristic()
);

$formater = new Formater(new RelativeTimestampLeapYear($calculator, null));

echo $calendar->format(new DateTime(), 'Y-m-d');

?>
```


Time formats
------------

The French Republican/Revolutionary calendar initially came with a decimal time
format (10h, 100m, 100s) replacing the duodecimal format (24h, 60m, 60s), but
was quickly abandonned, probably because of the difficuly and cost to replace
every clock in the country (because at that time, they didn't have smartwatches
able to upgrade their apps. Dark times.)

So, depending on how revolutionnary you feel, you can use one format or another,
by injecting the TimeConvertor suiting your needs.

Components will usually default to the actual DuoDecimal representation

```php
<?php

use Popy\RepublicanCalendar\Formater;
use Popy\RepublicanCalendar\Converter\RelativeTimestampLeapYear;
use Popy\RepublicanCalendar\TimeConverter\{DecimalTime,DuoDecimalTime};

$timeConverter = $yourRevolutionaryLevel > 9000
    ? new DecimalTime()
    : new DuoDecimalTime()
;

$formater = new Formater(new RelativeTimestampLeapYear(null, $timeConverter));


echo $calendar->format(new DateTime(), 'Y-m-d');

?>
```
