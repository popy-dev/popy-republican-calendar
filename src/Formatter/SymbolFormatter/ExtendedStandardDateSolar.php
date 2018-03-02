<?php

namespace Popy\RepublicanCalendar\Formatter\SymbolFormatter;

use Popy\Calendar\FormatterInterface;
use Popy\Calendar\Parser\FormatToken;
use Popy\Calendar\Formatter\SymbolFormatterInterface;
use Popy\Calendar\Formatter\LocalisationInterface;
use Popy\Calendar\Formatter\NumberConverterInterface;
use Popy\Calendar\ValueObject\DateRepresentationInterface;
use Popy\Calendar\ValueObject\DateSolarRepresentationInterface;

/**
 * Extended standard format, handling DateSolarRepresentationInterface.
 */
class ExtendedStandardDateSolar implements SymbolFormatterInterface
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
    public function formatSymbol(DateRepresentationInterface $input, FormatToken $token, FormatterInterface $formatter)
    {
        if (!$input instanceof DateSolarRepresentationInterface) {
            return;
        }

        if ($token->is('y')) {
            // y   A two digit representation of a year
            // Pointless, so converting it to roman numbers instead
            return $this->converter->to($input->getYear() ?: 0);
        }

        if ($token->is('X')) {
            // Added symbol : Day individual name
            return (string)$this->locale->getDayName('y' . $input->getDayIndex());
        }
    }
}
