<?php

namespace Popy\RepublicanCalendar;

use Popy\Calendar\FormaterInterface as CalendarFormaterInterface;
use Popy\RepublicanCalendar\Converter\DateTimeRepresentation\EgyptianDateTime;

/**
 * Extended FormaterInterface.
 */
interface FormaterInterface extends CalendarFormaterInterface
{
    /**
     * Formats an already converted EgyptianDateTime
     *
     * @param EgyptianDateTime $input
     * @param strong           $format @see self::format
     *
     * @return string
     */
    public function formatEgyptian(EgyptianDateTime $input, $format);
}
