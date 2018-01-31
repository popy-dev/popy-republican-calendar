<?php

namespace Popy\RepublicanCalendar\Formater;

use DateTimeInterface;
use Popy\RepublicanCalendar\SymbolFormater;
use Popy\RepublicanCalendar\FormaterInterface;
use Popy\RepublicanCalendar\ConverterInterface;

class RepublicanCalendar implements FormaterInterface
{
    /**
     * Symbol Formater
     *
     * @var SymbolFormater
     */
    protected $formater;

    /**
     * Date converter
     *
     * @var ConverterInterface
     */
    protected $converter;

    /**
     * Class constructor.
     *
     * @param SymbolFormater $formater
     * @param ConverterInterface $converter
     */
    public function __construct(SymbolFormater $formater, ConverterInterface $converter)
    {
        $this->formater = $formater;
        $this->converter = $converter;
    }

    /**
     * {@inheritDoc}
     * 
     * Date format : same as date() with :
     *  - D symbol added to match day individual name
     *  - | symbol to separate the "normal date" format and the optionnal "sans culottide date" format
     */
    public function format(DateTimeInterface $input, $format)
    {
        $rep = $this->converter->toRepublican($input);

        $string = str_split($format, 1);
        $skipNext = false;
        $res = '';

        foreach ($string as $k => $v) {
            if ($skipNext) {
                $skipNext = false;
                $res .= $v;
                continue;
            }

            if ($v === '\\') {
                $skipNext = true;
                continue;
            }

            if ($v === '|') {
                // Normal format / sans-culottide format separator
                if ($rep->getMonth() === 13) {
                    // Reset formated date and continue
                    $res = '';
                    continue;
                } else {
                    break;
                }
            }

            if (!$this->formater->handles($v)) {
                $res .= $input->format($v);
                continue;
            }

            $res .= $this->formater->format($rep, $v);
        }

        return $res;
    }
}