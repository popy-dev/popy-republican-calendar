French Republican Calendar
==========================

This is a popy/calendar implementation of the[French Republican/Revolutionary calendar.](https://en.wikipedia.org/wiki/French_Republican_Calendar),
which is a 12 monthes of 30 days calendar, with 5 (or 6 on leap years)
additional days which has been inspired from the Egyptian calendar.

Date convertors are no longer inspired from [caarmen's work](https://github.com/caarmen/french-revolutionary-calendar)
but her work helped me a lot to wrap my head around all the difficulties of
date conversion, so some of my earlier implementation may stay a while as a
tribute (but won't be maintained).

The dates conversion handles timezones and DST, which are keeped as regular hour
jumps, at the same dates/hours than with regular dates, which gives constistency
in a day.

Purpose
-------
It serves as an example/proof of concept, and as a sandbox. Code is tested there
and eventually, if it works and is 
This is an example implementation of popy/calendar interfaces, aswell as a 
sandbox (who said litter) and improvement lab.
Most of popy/calendar tools and interfaces originate from thsi repository.

Usage
-----

Every class constructor is callable without arguments, and will build their
dependencies if needed.

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
use Popy\RepublicanCalendar\Converter\PivotalDate;
use Popy\Calendar\Converter\LeapYearCalculator\{
    Futuristic,
    RommeWithContinuedImpairLeapDay
};

$calculator = new RommeWithContinuedImpairLeapDay(
    // Lets be prepared for Y4K !
    new Futuristic()
);

$formater = new Formater(new PivotalDate($calculator, null));

echo $calendar->format(new DateTime(), 'Y-m-d');

?>
```

Time formats
------------

The French Republican/Revolutionary calendar initially came with a decimal time
format (10h, 100m, 100s) just as the egyption inspiration, replacing the
duodecimal format (24h, 60m, 60s), but was quickly abandonned, probably because
of the difficuly and cost to replace every clock in the country (because at that
time, they didn't have smartwatches able to upgrade their apps. Dark times.)

So, depending on how revolutionnary you feel, you can use one format or another,
by injecting the TimeConvertor suiting your needs.

Components will usually default to the actual DuoDecimal representation.

Trivia : The Chinese time system divided the day in 100 equal parts (ke) of
14 minutes 24seconds, which is very close of our quarter hour, and matches
perfectly the 10 minutes in the decimal time system. Isn't that
interoperability ?

```php
<?php

use Popy\RepublicanCalendar\Formater;
use Popy\RepublicanCalendar\Converter\PivotalDate;
use Popy\Calendar\Converter\TimeConverter\{DecimalTime,DuoDecimalTime};

$timeConverter = $yourRevolutionaryLevel > 9000
    ? new DecimalTime()
    : new DuoDecimalTime()
;

$formater = new Formater(new PivotalDate(null, $timeConverter));


echo $calendar->format(new DateTime(), 'Y-m-d');

?>
```
