<?php

namespace Popy\RepublicanCalendar\Formatter;

use Popy\Calendar\Formatter\AgnosticFormatter;
use Popy\Calendar\ValueObject\DateRepresentationInterface;
use Popy\Calendar\ValueObject\DateFragmentedRepresentationInterface;

class ExtendedAgnosticFormatter extends AgnosticFormatter
{
    /**
     * @inheritDoc
     */
    public function formatDateRepresentation(DateRepresentationInterface $input, $format)
    {
        $res = '';
        $tokens = $this->lexer->tokenizeFormat($format);

        foreach ($tokens as $token) {
            // Handling of a special token |
            if ($token->is('|')) {
                if (
                    $input instanceof DateFragmentedRepresentationInterface
                    && $input->getDateParts()->get(0) === 12
                ) {
                    // 13rd 'fake' month : reset result, in order to use this alterative format
                    $res = '';
                    continue;
                }

                // Not the 13rd fake month, then the formating is done
                break;
            }

            $res .= $this->formatter->formatSymbol($input, $token, $this);
        }

        return $res;
    }
}
