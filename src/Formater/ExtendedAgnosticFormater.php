<?php

namespace Popy\RepublicanCalendar\Formater;

use Popy\Calendar\Formater\AgnosticFormater;
use Popy\Calendar\ValueObject\DateRepresentationInterface;
use Popy\Calendar\ValueObject\DateSolarRepresentationInterface;

class ExtendedAgnosticFormater extends AgnosticFormater
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
                    $input instanceof DateSolarRepresentationInterface
                    && $input->getDateParts()->get(0) === 12
                ) {
                    // 13rd 'fake' month : reset result, in order to use this alterative format
                    $res = '';
                    continue;
                }

                // Not the 13rd fake month, then the formating is done
                break;
            }

            $res .= $this->formater->formatSymbol($input, $token, $this);
        }

        return $res;
    }
}
