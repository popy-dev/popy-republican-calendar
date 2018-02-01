<?php

namespace Popy\RepublicanCalendar;

use DateTimeInterface;

/**
 * Republican Date <=> DateTime converter interface.
 */
interface ConverterInterface
{
    /**
     * Converts a DateTime to a Republican Date.
     * 
     * @param DateTime $input
     * 
     * @return Date
     */
    public function toRepublican(DateTimeInterface $input);

    /**
     * Converts a Republican Date into a DateTimeInterface
     * 
     * @param Date $input
     * 
     * @return DateTimeInterface
     */
    public function fromRepublican(Date $input);
}