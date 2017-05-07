<?php

namespace Popy\RepublicanCalendar;

class Formater
{
    /**
     * Symbol Formater
     *
     * @var SymbolFormater
     */
    protected $formater;

    /**
     * Class constructor.
     *
     * @param SymbolFormater $formater
     */
    public function __construct(SymbolFormater $formater)
    {
        $this->formater = $formater;
    }

    /**
     * Format a date into a string.
     * @param Date        $input               Input date.
     * @param string      $format              Date format (php/date compatible, with D symbol added to match day individual name)
     * @param string|null $sansCulottideFormat Alternate format for "sans-culottides" days (filler days).
     * 
     * @return string
     */
    public function format(Date $input, $format, $sansCulottideFormat = null)
    {
        $symbols = array(
            'y', 'Y', 'L',
            'F', 'm', 'n', 't',
            'd', 'j', 'l', 'S', 'w', 'z', 'N', 'D',
            'W'
        );

        if ($sansCulottideFormat !== null && $input->getMonth() === 13) {
            $format = $sansCulottideFormat;
        }

        $string = str_split($format, 1);
        $skipNext = false;

        foreach ($string as $k => $v) {
            if ($skipNext) {
                $skipNext = false;
                continue;
            }

            if ($v === '\\') {
                $skipNext = true;
                continue;
            }

            if (!in_array($v, $symbols)) {
                continue;
            }

            $string[$k] = $this->formater->format($input, $v);
        }

        return implode('', $string);
    }
}