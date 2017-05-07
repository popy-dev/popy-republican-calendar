<?php

namespace Popy\RepublicanCalendar\Tests;

use PHPUnit_Framework_TestCase;
use Popy\RepublicanCalendar\RomanConverter;

class RomanConverterTest extends PHPUnit_Framework_TestCase
{
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
        $converter = new RomanConverter();

        $this->assertSame($expected, $converter->decimalToRoman($input));
    }
}
