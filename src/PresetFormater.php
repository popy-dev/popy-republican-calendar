<?php

namespace Popy\RepublicanCalendar;

/**
 * Stores a preset date formatting to be used as a quick formater.
 */
class PresetFormater
{
    /**
     * Formater.
     *
     * @var Formater
     */
    protected $formater;
    
    /**
     * Preset format.
     *
     * @var string
     */
    protected $format;
    
    /**
     * Reset sansCulottideFormat
     *
     * @var string|null
     */
    protected $sansCulottideFormat;

    /**
     * Class constructor.
     *
     * @param Formater    $Formater            Formater
     * @param string      $format              Date format (php/date compatible, with D symbol added to match day individual name)
     * @param string|null $sansCulottideFormat Alternate format for "sans-culottides" days (filler days).
     */
    public function __construct(Formater $formater, $format, $sansCulottideFormat = null)
    {
        $this->formater = $formater;
        $this->format   = $format;
        $this->sansCulottideFormat = $sansCulottideFormat;
    }

    /**
     * Format the input date.
     *
     * @param Date $input
     * 
     * @return string
     */
    public function format(Date $input)
    {
        return $this->formater->format($input, $this->format, $this->sansCulottideFormat);
    }
}