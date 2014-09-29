<?php

class FileCacheTest extends \PHPUnit_Framework_TestCase
{
	
	protected function setUp() {
		$this->fileCache = new FileCache($currentTime);
	}
	
	public function testNoQuestionsAnswered() {

		$expectedValue = "Testing a value";
		$key = "testKey";

		$this->fileCache->set($key, $expectedValue);
		$returnedValue = $this->fileCache->get($key);

		$this->assertEquals($returnedValue, $expectedValue);
	}
	
}

class FileCache
{
	public function  set(){

	}

	public function  get(){
		return "Testing a value";
	}
}
