<?php

namespace Popy\RepublicanCalendar;

use DateTimeInterface;
use Popy\Calendar\FormaterInterface;
use Popy\RepublicanCalendar\Converter\Basic as BasicConverter;

class Formater implements FormaterInterface
{
    /**
     * Symbol Formater
     *
     * @var SymbolFormater
     */
    protected $formater;

    /**
     * Date converter.
     *
     * @var ConverterInterface
     */
    protected $converter;

    /**
     * Class constructor.
     *
     * @param SymbolFormater|null     $formater
     * @param ConverterInterface|null $converter
     */
    public function __construct(SymbolFormater $formater = null, ConverterInterface $converter = null)
    {
        $this->formater = $formater ?: new SymbolFormater();
        $this->converter = $converter ?: new BasicConverter();
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