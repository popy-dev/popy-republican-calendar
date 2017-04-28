<?php

namespace Popy\RepublicanCalendar;

use DateTime;

/**
 * Republican Date <=> DateTime converter interface.
 */
interface Converter
{
    /**
     * Converts a DateTime to a Republican Date.
     * 
     * @param DateTime $input
     * 
     * @return Date
     */
    public function toRepublican(DateTime $input);

    /**
     * Converts a Republican Date into a standard DateTime
     * 
     * @param Date $input
     * 
     * @return DateTime
     */
    public function fromRepublican(Date $input);
}