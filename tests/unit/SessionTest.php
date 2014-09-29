<?php

include_once __DIR__ . "/../../app/library/classes/storage/Session.class.php";

class SessionTest extends \PHPUnit_Framework_TestCase
{
	
	protected function setUp() {
		$this->session = new Session();
	}
	
	public function testSaveAndRetreiveValue() {
		
		$expectedValue = "Testing a value";
		$key = "testKey";
		
		$this->session->set($key, $expectedValue);
		$returnedValue = $this->session->get($key);
		
		$this->assertEquals($returnedValue, $expectedValue);
	}
	
	public function testSaveAndRetreiveMultipleValues() {
		
		$expectedValue1 = "Testing a value1";
		$key1 = "testKey1";
		$expectedValue2 = "oh word";
		$key2 = "testKey2";
		
		$this->session->set($key1, $expectedValue1);
		$this->session->set($key2, $expectedValue2);
		$returnedValue1 = $this->session->get($key1);
		$returnedValue2 = $this->session->get($key2);
		
		$this->assertEquals($returnedValue1, $expectedValue1);
		$this->assertEquals($returnedValue2, $expectedValue2);
	}
	
	public function testSaveAndRetreiveWithOddKey() {
		
		$expectedValue = "Testing a value";
		$key = "test^&*(789fh8974&^%(+__)(()'Key";
		
		$this->session->set($key, $expectedValue);
		$returnedValue = $this->session->get($key);
		
		$this->assertEquals($returnedValue, $expectedValue);
	}
	
	public function testSaveAndRetreiveWithInvalidKey() {
		
		$key = "This key doesn't exist";
		$returnedValue = $this->session->get($key);
		
		$this->assertEquals(false, $returnedValue);
	}
	
	public function testSaveAndRetreiveAndDeleteValue() {
		
		$expectedValue = "This is the value";
		$key = "woahKey";

		$this->session->forget($key);
		$returnedValue = $this->session->get($key);
		
		$this->assertEquals($returnedValue, false);
		
		$this->session->set($key, $expectedValue);
		$returnedValue = $this->session->get($key);
		
		$this->assertEquals($returnedValue, $expectedValue);
		
		$this->session->forget($key);
		$returnedValue = $this->session->get($key);
		
		$this->assertEquals($returnedValue, false);
	}
}