French Republican Calendar
==========================

This repository holds a half-assed popy/calendar implementation of the French
Republican/Revolutionary calendar.

https://en.wikipedia.org/wiki/French_Republican_Calendar

Date convertors are inspired from [caarmen's work](https://github.com/caarmen/french-revolutionary-calendar).

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