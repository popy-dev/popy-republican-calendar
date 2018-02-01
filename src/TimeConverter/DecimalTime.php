<?php

namespace Popy\RepublicanCalendar\TimeConverter;

use DateTimeInterface;
use Popy\RepublicanCalendar\RepublicanDateTime;
use Popy\RepublicanCalendar\Utility\TimeConverter;
use Popy\RepublicanCalendar\TimeConverterInterface;

/**
 * Egyptian/Republican decimal time converter.
 */
class DecimalTime implements TimeConverterInterface
{
    /**
     * Time conversion utility.
     *
     * @var TimeConverter
     */
    protected $converter;

    /**
     * Decimal time parts ranges.
     *
     * @var array<int>
     */
    static $decimalRanges = [10, 100, 100, 1000000];

    /**
     * Duodecimal time parts ranges.
     *
     * @var array<int>
     */
    static $duoDecimalRanges = [24, 60, 60, 1000000];

    /**
     * Class constructor.
     *
     * @param TimeConverter|null $converter Time conversion utility.
     */
    public function __construct(TimeConverter $converter = null)
    {
        $this->converter = $converter ?: new TimeConverter();
    }

    /**
     * @inheritDoc
     */
    public function toRepublicanTime(DateTimeInterface $input)
    {
        list($hour, $min, $seconds, $micro) = explode(':', $input->format('H:i:s:u'));

        return $this->converter->convertTime(
            [$hour, $min, $seconds, $micro],
            static::$duoDecimalRanges,
            static::$decimalRanges
        );
    }

    /**
     * @inheritDoc
     */
    public function fromRepublicanTime(RepublicanDateTime $input)
    {
        return $this->converter->convertTime(
            [
                $input->getHour(),
                $input->getMinute(),
                $input->getSecond(),
                $input->getMicrosecond(),
            ],
            static::$decimalRanges,
            static::$duoDecimalRanges
        );
    }
}