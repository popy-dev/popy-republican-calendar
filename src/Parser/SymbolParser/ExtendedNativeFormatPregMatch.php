<?php

namespace Popy\RepublicanCalendar\Parser\SymbolParser;

use Popy\Calendar\Parser\FormatToken;
use Popy\Calendar\Parser\DateLexer\PregSimple;
use Popy\Calendar\Parser\FormatParserInterface;
use Popy\Calendar\Parser\SymbolParser\PregNative;
use Popy\Calendar\Formater\LocalisationInterface;
use Popy\RepublicanCalendar\Formater\Localisation\RepublicanHardcodedFrench;

class PregExtendedNative extends PregNative
{
    /**
     * Localisation
     *
     * @var LocalisationInterface
     */
    protected $locale;

    /**
     * Class constructor.
     *
     * @param LocalisationInterface|null $locale
     */
    public function __construct(LocalisationInterface $locale = null)
    {
        $this->locale = $locale ?: new RepublicanHardcodedFrench();
    }

    /**
     * @inheritDoc
     */
    public function parseSymbol(FormatToken $token, FormatParserInterface $formater)
    {
        if ($token->is('y')) {
            // Roman year
            return new PregSimple($token, '-?[MCDLXVI]+');
        }

        if ($token->is('X')) {
            // Added symbol : Day individual name
            return new PregSimple($token, '\S.*?');
        }

        return parent::parseSymbol($token, $formater);
    }
}
