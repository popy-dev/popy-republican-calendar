<?php

namespace Popy\RepublicanCalendar\Parser\SymbolParser;

use Popy\Calendar\Parser\FormatToken;
use Popy\Calendar\Parser\DateLexer\PregSimple;
use Popy\Calendar\Parser\DateLexer\PregChoice;
use Popy\Calendar\Parser\SymbolParserInterface;
use Popy\Calendar\Parser\FormatParserInterface;
use Popy\Calendar\Formatter\LocalisationInterface;
use Popy\Calendar\Formatter\NumberConverterInterface;

class PregExtendedNative implements SymbolParserInterface
{
    /**
     * Locale.
     *
     * @var LocalisationInterface
     */
    protected $locale;

    /**
     * Roman number convertor.
     *
     * @var NumberConverterInterface
     */
    protected $converter;

    /**
     * Class constructor.
     *
     * @param LocalisationInterface    $locale
     * @param NumberConverterInterface $converter
     */
    public function __construct(LocalisationInterface $locale, NumberConverterInterface $converter)
    {
        $this->locale = $locale;
        $this->converter = $converter;
    }

    /**
     * @inheritDoc
     */
    public function parseSymbol(FormatToken $token, FormatParserInterface $formatter)
    {
        if ($token->is('y')) {
            // Roman year
            // TODO : here we are assuming the number format, while any number
            // converter could have been injected.
            $lexer = new PregSimple($token, '-?[MCDLXVI]+');
            $lexer->setCallback(array($this, 'parseRomanCallback'));

            return $lexer;
        }

        if ($token->is('X')) {
            // Added symbol : Day individual name
            return $this->buildDayIndividualNamesLexer($token->withAlias('z'));
        }
    }

    /**
     * Builds a choice lexer based on a get*name localisation method.
     *
     * @param FormatToken $token Token.
     * 
     * @return PregChoice
     */
    protected function buildDayIndividualNamesLexer(FormatToken $token)
    {
        $choices = [];
        $i = 0;

        while (null !== $label = $this->locale->getDayName('y' . ($i++))) {
            $choices[] = $label;
        }

        return new PregChoice($token, $choices);
    }

    /**
     * Callable callback accessing internal converter.
     *
     * @param PregSimple $lexer Lexer matching the number.
     * @param string     $res   Number representation.
     *
     * @return integer|string|null
     */
    public function parseRomanCallback(PregSimple $lexer, $res)
    {
        return $this->converter->from($res);
    }
}
