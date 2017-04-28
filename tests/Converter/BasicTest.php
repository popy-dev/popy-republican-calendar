<?php

namespace Popy\RepublicanCalendar\Tests\Converter;

use PHPUnit_Framework_TestCase;
use Popy\RepublicanCalendar\Converter\Basic;

class BasicTest extends PHPUnit_Framework_TestCase
{
    public function testToRepublican()
    {
        $input = $this->getMock('DateTime');
        $converter = new Basic();

        $input
            ->expects($this->once())
            ->method('format')
            ->with('Y-L-z')
            ->will($this->returnValue('1792-1-265'))
        ;

        $result = $converter->toRepublican($input);

        $this->assertSame(1, $result->getYear(), 'Year');
        $this->assertSame(1, $result->getMonth(), 'Month');
        $this->assertSame(1, $result->getDay(), 'Day');
    }
}
