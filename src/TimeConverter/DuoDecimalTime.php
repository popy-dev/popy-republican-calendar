<?php

namespace Popy\RepublicanCalendar\TimeConverter;

use DateTimeInterface;
use Popy\RepublicanCalendar\RepublicanDateTime;
use Popy\RepublicanCalendar\TimeConverterInterface;

/**
 * Duodecimal time converter.
 */
class DuoDecimalTime implements TimeConverterInterface
{
    /**
     * @inheritDoc
     */
    public function toRepublicanTime(DateTimeInterface $input)
    {
        return array_map(
            'intval',
            explode(':', $input->format('H:i:s:u'))
        );
    }

    /**
     * @inheritDoc
     */
    public function fromRepublicanTime(RepublicanDateTime $input)
    {
        return [
            $input->getHour(),
            $input->getMinute(),
            $input->getSecond(),
            $input->getMicrosecond(),
        ];
    }
}