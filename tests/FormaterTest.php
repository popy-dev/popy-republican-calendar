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
        $symbol = $this->getMockBuilder('Popy\RepublicanCalendar\SymbolFormater')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $formater = new Formater($symbol);

        $symbol
            ->expects($this->once())
            ->method('format')
            ->with($input, 'd')
            ->will($this->returnValue('%%%'))
        ;

        $result = $formater->format($input, 'a b c d e');


        $this->assertSame('a b c %%% e', $result);
    }

}
