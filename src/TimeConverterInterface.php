<?php

namespace Popy\RepublicanCalendar;

use DateTimeInterface;

/**
 * Republican Date <=> DateTime Time converter interface.
 */
interface TimeConverterInterface
{
    /**
     * Converts a regular time (H:i:s:u) into a republican time (as array).
     *
     * @param DateTimeInterface $input
     *
     * @return array<int> [hours, minutes, seconds, microseconds]
     */
    public function toRepublicanTime(DateTimeInterface $input);

    /**
     * Converts a republican time (H:i:s:u) into a regular time (as array).
     *
     * @param RepublicanDateTime $input
     *
     * @return array<int> [hours, minutes, seconds, microseconds]
     */
    public function fromRepublicanTime(RepublicanDateTime $input);
}