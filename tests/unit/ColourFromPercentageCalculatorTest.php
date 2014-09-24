<?php

include_once __DIR__ . "/../../app/library/classes/presentation/ColourFromPercentageCalculator.class.php";

class ColourFromPercentageCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $ColourFromPercentageCalculator;
    
    protected function setUp() {
        $this->ColourFromPercentageCalculator = new ColourFromPercentageCalculator();
    }
    
    public function testDifferentValues() {
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(0), "#FF0000");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(5), "#FF0000");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(69), "#FF0000");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(70), "#CC6600");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(71), "#CC6600");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(79), "#CC6600");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(80), "#008000");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(81), "#008000");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(99), "#008000");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(100), "#008000");
        $this->assertEquals($this->ColourFromPercentageCalculator->calculate(101), "#008000");
    }
}
