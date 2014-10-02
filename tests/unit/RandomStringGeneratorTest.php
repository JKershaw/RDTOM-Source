<?php
include_once __DIR__ . "/../../app/library/classes/misc/RandomStringGenerator.class.php";

class RandomStringGeneratorTest extends \PHPUnit_Framework_TestCase
{
	public function testGenerateRandomString() {
		
		$randomStringGenerator = new RandomStringGenerator();

		$stringLength = 50;
		$randomString = $randomStringGenerator->generate($stringLength);
		$this->assertEquals(strlen($randomString), $stringLength);
		
		$stringLength = 500;
		$randomString = $randomStringGenerator->generate($stringLength);
		$this->assertEquals(strlen($randomString), $stringLength);
	}
}