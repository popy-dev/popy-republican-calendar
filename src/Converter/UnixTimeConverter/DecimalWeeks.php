<?php

namespace Popy\RepublicanCalendar\Converter\UnixTimeConverter;

use Popy\Calendar\Converter\Conversion;
use Popy\Calendar\Converter\UnixTimeConverterInterface;
use Popy\Calendar\ValueObject\DateSolarRepresentationInterface;
use Popy\Calendar\ValueObject\DateFragmentedRepresentationInterface;

/**
 * Decimal weeks.
 */
class DecimalWeeks implements UnixTimeConverterInterface
{

    /**
     * @inheritDoc
     */
    public function fromUnixTime(Conversion $conversion)
    {
        $input = $conversion->getTo();

        if (
            !$input instanceof DateFragmentedRepresentationInterface
            || !$input instanceof DateSolarRepresentationInterface
        ) {
            return;
        }

        $dateParts = $input->getDateParts()->withTransversals([
            $input->getYear(),
            intval($input->getDayIndex() / 10),
            $input->getDayIndex() % 10
        ]);

        $conversion->setTo($input->withDateParts($dateParts));
    }

    /**
     * @inheritDoc
     */
    public function toUnixTime(Conversion $conversion)
    {
        $input = $conversion->getTo();

        if (
            !$input instanceof DateFragmentedRepresentationInterface
            || !$input instanceof DateSolarRepresentationInterface
        ) {
            return;
        }

        if (null !== $input->getDayIndex() && null !== $input->getYear()) {
            return ;
        }

        $year = $input->getDateParts()->getTransversal(0);
        $weekIndex = $input->getDateParts()->getTransversal(1);
        $dayOfWeek = (int)$input->getDateParts()->getTransversal(2);

        if (null === $year || null === $weekIndex) {
            // Too imprecise to be worth
            return;
        }

        $dayIndex = $dayOfWeek + $weekIndex * 10;

        $input = $input
            ->withYear($year, $input->isLeapYear())
            ->withDayIndex($dayIndex, $input->getEraDayIndex())
        ;

        $conversion->setTo($input);
    }

}
