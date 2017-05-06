<?php

namespace Popy\RepublicanCalendar\Tests;

use PHPUnit_Framework_TestCase;
use Popy\RepublicanCalendar\Formater;

class FormaterTest extends PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $input = $this->getMockBuilder('Popy\RepublicanCalendar\Date')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $locale = $this->getMock('Popy\RepublicanCalendar\LocaleInterface');
        $formater = new Formater($locale);

        $input
            ->expects($this->once())
            ->method('getYear')
            ->will($this->returnValue(200))
        ;
        $input
            ->expects($this->exactly(2))
            ->method('getMonth')
            ->will($this->returnValue(3))
        ;
        $input
            ->expects($this->exactly(4))
            ->method('getDay')
            ->will($this->returnValue(5))
        ;

        $locale
            ->expects($this->once())
            ->method('getWeekDayName')
            ->with(4)
            ->will($this->returnValue('%DAY%'))
        ;

        $locale
            ->expects($this->once())
            ->method('getNumberOrdinalSuffix')
            ->with(5)
            ->will($this->returnValue('%SUFFIX%'))
        ;

        $locale
            ->expects($this->once())
            ->method('getMonthName')
            ->with(3)
            ->will($this->returnValue('%MONTH%'))
        ;

        $locale
            ->expects($this->once())
            ->method('getDayName')
            ->with(64)
            ->will($this->returnValue('%DAYNAME%'))
        ;

        $result = $formater->format($input, 'l dS F Y D');

        $this->assertSame('%DAY% 05%SUFFIX% %MONTH% 200 %DAYNAME%', $result);
    }

    public function testFormatrSwitchesToAlternateFormat()
    {
        $input = $this->getMockBuilder('Popy\RepublicanCalendar\Date')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $locale = $this->getMock('Popy\RepublicanCalendar\LocaleInterface');
        $formater = new Formater($locale);

        $input
            ->expects($this->once())
            ->method('getMonth')
            ->will($this->returnValue(13))
        ;

        $result = $formater->format($input, '%', '%%');

        $this->assertSame('%%', $result);
    }

    public function decimalToRomanProvider()
    {
        return array(
            array(1, 'I'),
            array(2, 'II'),
            array(3, 'III'),
            array(4, 'IV'),
            array(18, 'XVIII'),
            array(19, 'XIX'),
        );
    }

    /**
     * @dataProvider decimalToRomanProvider
     */
    public function testDecimalToRoman($input, $expected)
    {
        $locale = $this->getMock('Popy\RepublicanCalendar\LocaleInterface');
        $formater = new Formater($locale);

        $this->assertSame($expected, $formater->decimalToRoman($input));
    }
}
