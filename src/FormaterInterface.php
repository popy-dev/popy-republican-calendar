<?php

namespace Popy\RepublicanCalendar;

use DateTimeInterface;

interface FormaterInterface
{
    /**
     * Format a date into a string.
     *
     * @param DateTimeInterface $input  Input date.
     * @param string            $format Date format
     * 
     * @return string
     */
    public function format(DateTimeInterface $input, $format);
}