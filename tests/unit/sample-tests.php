<?php
class SampleTest extends PHPUnit_Framework_TestCase
{
    public function testReturnsZero()
    {
        $true = true;

        $this->assertEquals(true, $true);
    }
}