<?php

namespace Popy\RepublicanCalendar\Parser\SymbolParser;

use Popy\Calendar\Parser\FormatToken;
use Popy\Calendar\Parser\DateLexer\PregSimple;
use Popy\Calendar\Parser\DateLexer\PregChoice;
use Popy\Calendar\Parser\SymbolParserInterface;
use Popy\Calendar\Parser\FormatParserInterface;
use Popy\Calendar\Formatter\LocalisationInterface;
use Popy\Calendar\Formatter\Utility\RomanConverter;
use Popy\RepublicanCalendar\Formatter\Localisation\RepublicanHardcodedFrench;

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
     * @var RomanConverter
     */
    protected $converter;

    /**
     * Class constructor.
     *
     * @param LocalisationInterface|null $locale
     * @param RomanConverter|null        $converter
     */
    public function __construct(LocalisationInterface $locale = null, RomanConverter $converter = null)
    {
        $this->locale = $locale ?: new RepublicanHardcodedFrench();
        $this->converter = $converter ?: new RomanConverter();
    }

    /**
     * @inheritDoc
     */
    public function parseSymbol(FormatToken $token, FormatParserInterface $formatter)
    {
        if ($token->is('y')) {
            // Roman year
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
     * @param string      $x     Method name middle part.
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

    public function parseRomanCallback($lexer, $res)
    {
        return $this->converter->romanToDecimal($res);
    }
}
