<?php

namespace Popy\RepublicanCalendar\Formater;

use DateTimeInterface;
use Popy\RepublicanCalendar\FormaterInterface;

class GregorianCalendar implements FormaterInterface
{
    /**
     * {@inheritDoc}
     * 
     * Date format : same as date() with :
     *  - D symbol added to match day individual name
     *  - | symbol to separate the "normal date" format and the optionnal "sans culottide date" format
     */
    public function format(DateTimeInterface $input, $format)
    {
        return $input->format($format);
    }
}