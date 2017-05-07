<?php

namespace Popy\RepublicanCalendar\Tests;

use PHPUnit_Framework_TestCase;
use Popy\RepublicanCalendar\SymbolFormater;

class SymbolFormaterTest extends PHPUnit_Framework_TestCase
{
    public function testFormaty()
    {
        $input = $this->getMockBuilder('Popy\RepublicanCalendar\Date')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $locale = $this->getMock('Popy\RepublicanCalendar\LocaleInterface');
        $converter = $this->getMock('Popy\RepublicanCalendar\RomanConverter');
        $formater = new SymbolFormater($locale, $converter);

        $input
            ->expects($this->once())
            ->method('getYear')
            ->will($this->returnValue(200))
        ;

        $converter
            ->expects($this->once())
            ->method('decimalToRoman')
            ->with(200)
            ->will($this->returnValue('OK'))
        ;

        $result = $formater->format($input, 'y');

        $this->assertSame('OK', $result);
    }
}
