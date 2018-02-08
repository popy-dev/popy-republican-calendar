<?php

namespace Popy\RepublicanCalendar\Formater\SymbolFormater;

use Popy\Calendar\FormaterInterface;
use Popy\Calendar\Parser\FormatToken;
use Popy\Calendar\Formater\Utility\RomanConverter;
use Popy\Calendar\Formater\SymbolFormaterInterface;
use Popy\Calendar\Formater\LocalisationInterface;
use Popy\Calendar\ValueObject\DateRepresentationInterface;
use Popy\Calendar\ValueObject\DateSolarRepresentationInterface;
use Popy\RepublicanCalendar\Formater\Localisation\RepublicanHardcodedFrench;

/**
 * Extended standard format, handling DateSolarRepresentationInterface.
 */
class ExtendedStandardDateSolar implements SymbolFormaterInterface
{
    /**
     * Locale (used for day & month names)
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
    public function formatSymbol(DateRepresentationInterface $input, FormatToken $token, FormaterInterface $formater)
    {
        if (!$input instanceof DateSolarRepresentationInterface) {
            return;
        }

        if ($token->is('y')) {
            // y   A two digit representation of a year
            // Pointless, so converting it to roman numbers instead
            return $this->converter->decimalToRoman($input->getYear());
        }

        if ($token->is('X')) {
            // Added symbol : Day individual name
            return $this->locale->getDayName('y' . $input->getDayIndex());
        }
    }
}
