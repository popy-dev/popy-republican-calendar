French Republican Calendar
==========================

This is a popy/calendar implementation of the[French Republican/Revolutionary calendar.](https://en.wikipedia.org/wiki/French_Republican_Calendar),
which is a 12 monthes of 30 days calendar, with 5 (or 6 on leap years)
additional days which has been inspired from the Egyptian calendar.

Date convertors are no longer inspired from [caarmen's work](https://github.com/caarmen/french-revolutionary-calendar)
but her work helped me a lot to wrap my head around all the difficulties of
date conversion.

The dates conversion handles timezones and DST, which are keeped as regular hour
jumps, at the same dates/hours than with regular dates, which gives constistency
in a day.

Other calendars
---------------

As said earlier, the revolutionary calendar is based on the egyptian one, so the
```CalendarFactory``` have a dedicated ```buildEgyptian``` method that initializes
an Egyptian calendar, just using configuration (but this is achievable with only the popy/calendar
package). Some egyptian calendar variations may be added aswell :

- Coptic
- Ethiopian

Purpose
-------

It serves as an example/proof of concept, and as a sandbox. Code is tested there
and eventually, if it works and is 
This is an example implementation of popy/calendar interfaces, aswell as a 
sandbox (who said litter) and improvement lab.
Most of popy/calendar tools and interfaces originate from this repository.

Usage
-----

The easiest way to get a proper republican calendar is to use the configurable factory.

Usage is basically the same as any popy/php-calendar implementation, with few additions :
- ```X``` will output the day individual name
- ```|``` seperates 2 alternatives, the first is used for regular date, the second alternative,
    if present, is used for the complementary day.

```php
<?php

use Popy\RepublicanCalendar\Factory\CalendarFactory;

$factory = new CalendarFactory();

$calendar = $factory->buildRepublican();

echo $calendar->format(new DateTime(), 'Y-m-d') . "\n";
echo $calendar->format(new DateTime(), 'l jS F y, X|F, X, y H:i:s') . "\n";

?>
```

Calculation Methods
-------------------

The initial calendar definition had vague rules on where to insert leap days,
and it was abandonned before a solution has been decided (or needed). Multiple
leap days calculators are available, in addition with calculator provided by
popy/calendar :

- RommeOddLeapDay : Wrapps any calculator, but transmit a
    different year value in order to make sure that the leap years will match
    the first 20 years leaps.
- RommeWithFixedLeapDay : wrapps any calculator, will transmit a different year
    during the first 20 years (same reason than above) then will become
    transparent.

CalendarFactory will defaults on a RommeOddLeapDay/Modern couple.

```php
<?php

use Popy\RepublicanCalendar\Factory\CalendarFactory;

$factory = new CalendarFactory();

$calendar = $factory->buildRepublican([
    // Lets use a better leap year calculator
    'leap' => 'futuristic',

    // Will wrap it with RommeWithFixedLeapDay
    'leap_wrapper' => 'fixed',
]);

echo $calendar->format(new DateTime(), 'Y-m-d');

?>
```

Format alternatives
-------------------

popy/republican-calendar brings a special formatter that will use a format alternative, if given,
to format the egyptian complementary month, using a | as separator, mirroring the behaviour of the
popy/calendar native format parser (which means the same format string can be used to format, then
parse the date, and will match both alternatives).

```php
<?php

use Popy\RepublicanCalendar\Factory\CalendarFactory;

$factory = new CalendarFactory();

$calendar = $factory->buildRepublican();

$format = 'l jS F Y, X|F, X, Y';

// WIll output "Primidi 1e Vendémiaire 0001, Raisin"
echo $calendar->format(new DateTime('1792-09-22 00:00:00'), $format) . "\n";
// Will output "Sans-culottides, jour de la révolution, 0019"
echo $calendar->format(new DateTime('1811-09-23 00:00:00'), $format) . "\n";

// Will output 1792-09-22
echo $calendar->parse('Primidi 1e Vendémiaire 0001, Raisin', $format)
    ->format('Y-m-d') . "\n"
;
// Will output 1811-09-23
echo $calendar->parse('Sans-culottides, jour de la révolution, 0019', $format)
    ->format('Y-m-d') . "\n"
;
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

CalendarFactory will default to the actual DuoDecimal representation.

Trivia : The Chinese time system divided the day in 100 equal parts (ke) of
14 minutes 24seconds, which is very close of our quarter hour, and matches
perfectly the 10 minutes in the decimal time system. Isn't that
interoperability ?

```php
<?php

use Popy\RepublicanCalendar\Factory\CalendarFactory;

$factory = new CalendarFactory();

$calendar = $factory->buildRepublican([
    'time_format' => $yourRevolutionaryLevel > 9000 ? 'decimal' : 'duodecimal',
]);

echo $calendar->format(new DateTime(), 'Y-m-d H:i:s');

?>
```
