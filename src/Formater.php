<?php

namespace Popy\RepublicanCalendar;

use DateTimeInterface;
use Popy\Calendar\FormaterInterface;
use Popy\Calendar\Formater\FormatLexerTrait;
use Popy\RepublicanCalendar\Converter\TimeConverter;

class Formater implements FormaterInterface
{
    use FormatLexerTrait;

    /**
     * Date converter.
     *
     * @var ConverterInterface
     */
    protected $converter;

    /**
     * Symbol Formater
     *
     * @var SymbolFormater
     */
    protected $formater;

    /**
     * Class constructor.
     *
     * @param SymbolFormater|null     $formater
     * @param ConverterInterface|null $converter
     */
    public function __construct(ConverterInterface $converter = null, SymbolFormater $formater = null)
    {
        $this->converter = $converter ?: new TimeConverter();
        $this->formater  = $formater  ?: new SymbolFormater();
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
        return $this->doFormat(
            $this->converter->toRepublican($input),
            $format
        );
    }

    /**
     * @inheritDoc
     */
    protected function formatSymbol(&$res, $input, $symbol)
    {
        if ($symbol !== '|') {
            $res .= $this->formater->format($input, $symbol);

            return true;
        }

        if ($input->getMonth() !== 13) {
            return false;
        }

        // Reset formated date and continue
        $res = '';
        return true;
    }
}