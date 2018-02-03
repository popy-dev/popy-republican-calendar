<?php

namespace Popy\RepublicanCalendar\Formater\Localisation;

use Popy\RepublicanCalendar\Formater\LocalisationInterface;

/**
 * Hardcoded monthes names from the Middle Kingdom era.
 *
 * @link https://en.wikipedia.org/wiki/Egyptian_calendar#Months
 */
class EgyptianHardcodedEgyptian implements LocalisationInterface
{
    /**
    * {@inheritDoc}
    */
    public function getMonthName($month)
    {
        $names = array(
            'Tḫy',
            'Mnht',
            'Ḥwt-ḥwr',
            'KꜢ-ḥr-KꜢ',
            'Sf-Bdt',
            'Rḫ Wr',
            'Rḫ Nds',
            'Rnwt',
            'Ḫnsw',
            'Hnt-htj',
            'Ipt-hmt',
            'Wp Rnpt',
            '',
       );

        if (isset($names[$month - 1])) {
            return $names[$month - 1];
        }
    }

    /**
    * {@inheritDoc}
    */
    public function getMonthShortName($month)
    {
        return $this->getMonthName($month);
    }

    /**
    * {@inheritDoc}
    */
    public function getDayName($day)
    {
    }

    /**
    * {@inheritDoc}
    */
    public function getDayShortName($month)
    {
    }

    /**
    * {@inheritDoc}
    */
    public function getIndividualDayName($day)
    {
    }

    /**
    * {@inheritDoc}
    */
    public function getNumberOrdinalSuffix($number)
    {
    }
}
