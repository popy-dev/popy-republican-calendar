<?php

namespace Popy\RepublicanCalendar\Converter;

use DateTimeInterface;
use Popy\RepublicanCalendar\RepublicanDateTime;
use Popy\RepublicanCalendar\ConverterInterface;
use Popy\RepublicanCalendar\TimeConverterInterface;
use Popy\RepublicanCalendar\TimeConverter\DecimalTime;

/**
 * Time converter implementation : relies on another converter for the date,
 *     then handles and overrides the time.
 */
class TimeConverter implements ConverterInterface
{
    /**
     * Internal converter.
     *
     * @var ConverterInterface
     */
    protected $converter;
    
    /**
     * Time converter.
     *
     * @var TimeConverterInterface
     */
    protected $timeConverter;

    /**
     * Class constructor.
     *
     * @param ConverterInterface|null     $converter     Internal converter.
     * @param TimeConverterInterface|null $timeConverter Time converter.
     */
    public function __construct(ConverterInterface $converter = null, TimeConverterInterface $timeConverter = null)
    {
        $this->converter = $converter ?: new RelativeTimestampLeapYear();
        $this->timeConverter = $timeConverter ?: new DecimalTime();
    }

    /**
     * {@inheritDoc}
     */
    public function toRepublican(DateTimeInterface $input)
    {
        $time = $this->timeConverter->toRepublicanTime($input);

        return $this->converter->toRepublican($input)
            ->setTime($time[0], $time[1], $time[2], $time[3])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function fromRepublican(RepublicanDateTime $input)
    {
        $time = $this->timeConverter->fromRepublicanTime($input);
        $result =  $this->converter->fromRepublican($input);

        if (PHP_VERSION_ID < 71000) {
            return $result->setTime($time[0], $time[1], $time[2]);
        }

        return $result->setTime($time[0], $time[1], $time[2], $time[3]);
    }
}