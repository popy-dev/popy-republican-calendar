<?php

namespace Popy\RepublicanCalendar\Formater;

use Popy\Calendar\Formater\LocalisationInterface as CalendarLocalisationInterface;

/**
 * Extended LocalisationInterface.
 */
interface LocalisationInterface extends CalendarLocalisationInterface
{
    /**
    * Get individual day name.
    *
    * @param mixed $day Day identifier.
    *
    * @return string|null
    */
    public function getIndividualDayName($day);
}
