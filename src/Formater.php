<?php

namespace Popy\RepublicanCalendar;

use DateTimeInterface;
use Popy\Calendar\ConverterInterface;
use Popy\Calendar\Parser\FormatLexerInterface;
use Popy\Calendar\Parser\FormatLexer\MbString;
use Popy\RepublicanCalendar\Formater\SymbolFormater;
use Popy\RepublicanCalendar\Converter\RepublicanPivotalDate;
use Popy\RepublicanCalendar\Converter\DateTimeRepresentation\EgyptianDateTime;

class Formater implements FormaterInterface
{
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
     * Format lexer
     * 
     * @var FormatLexerInterface
     */
    protected $lexer;

    /**
     * Class constructor.
     *
     * @param SymbolFormater|null     $formater
     * @param ConverterInterface|null $converter
     */
    public function __construct(ConverterInterface $converter = null, SymbolFormater $formater = null, FormatLexerInterface $lexer = null)
    {
        $this->converter = $converter ?: new RepublicanPivotalDate();
        $this->formater  = $formater ?: new SymbolFormater();
        $this->lexer     = $lexer ?: new MbString();
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
        return $this->formatEgyptian(
            $this->converter->fromDateTimeInterface($input),
            $format
        );
    }

    /**
     * Formats an already converted EgyptianDateTime
     *
     * @param EgyptianDateTime $input
     * @param strong           $format @see self::format
     *
     * @return string
     */
    public function formatEgyptian(EgyptianDateTime $input, $format)
    {
        $res = '';
        $tokens = $this->lexer->tokenizeFormat($format);

        foreach ($tokens as $token) {
            // Litteral tokens are returned raw
            if ($token->isLitteral()) {
                $res .= $token->getValue();
                continue;
            }

            // Non litterals & symbols (only EOF for now)
            if (!$token->isSymbol()) {
                break;
            }

            if (!$token->is('|')) {
                $res .= $this->formater->format($input, $token->getValue(), $this);

                continue;
            }

            // Character is a | separating normal format from special month
            // format. If month is not the special one, formating is done.
            if ($input->getMonth() !== 13) {
                return $res;
            }

            $res = '';
        }

        return $res;
    }
}
