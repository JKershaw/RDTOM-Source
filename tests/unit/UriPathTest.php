<?php
include_once __DIR__ . "/../../app/library/classes/routing/UriPath.class.php";

class UriPathTest extends \PHPUnit_Framework_TestCase
{
	public function testEmptyUriPath() {
		
		$_SERVER['REQUEST_URI'] = "/";
		$this->assertEquals(false, UriPath::part(0));
		
		$_SERVER['REQUEST_URI'] = "";
		$this->assertEquals(false, UriPath::part(0));
	}
	
	public function testBasicUriPaths() {
		
		$_SERVER['REQUEST_URI'] = "/foo/";
		$this->assertEquals("foo", UriPath::part(0));
		$this->assertEquals(false, UriPath::part(1));
		
		$_SERVER['REQUEST_URI'] = "/foo";
		$this->assertEquals("foo", UriPath::part(0));
		$this->assertEquals(false, UriPath::part(1));
		
		$_SERVER['REQUEST_URI'] = "/foo/bar/choo";
		$this->assertEquals("foo", UriPath::part(0));
		$this->assertEquals("bar", UriPath::part(1));
		$this->assertEquals("choo", UriPath::part(2));
	}
	
	public function testUriPathswithNoLeadingSlash() {
		
		$_SERVER['REQUEST_URI'] = "foo";
		$this->assertEquals("foo", UriPath::part(0));
		
		$_SERVER['REQUEST_URI'] = "foo/bar/choo";
		$this->assertEquals("foo", UriPath::part(0));
		$this->assertEquals("bar", UriPath::part(1));
	}
	
	public function testUriPathWithKeys() {
		
		$_SERVER['REQUEST_URI'] = "/foo/?rawr=ohnoes";
		$this->assertEquals("foo", UriPath::part(0));
		
		$_SERVER['REQUEST_URI'] = "/foo?rawr=ohnoes";
		$this->assertEquals("foo", UriPath::part(0));
		
		$_SERVER['REQUEST_URI'] = "/foo/bar/?rawr=ohnoes";
		$this->assertEquals("foo", UriPath::part(0));
		$this->assertEquals("bar", UriPath::part(1));
		
		$_SERVER['REQUEST_URI'] = "/foo/bar?rawr=ohnoes";
		$this->assertEquals("foo", UriPath::part(0));
		$this->assertEquals("bar", UriPath::part(1));
	}
	
	public function testUriPathWithCapitals() {
		
		$_SERVER['REQUEST_URI'] = "/FOO/";
		$this->assertEquals("foo", UriPath::part(0));
		
		$_SERVER['REQUEST_URI'] = "/foo/Bar/chOO";
		$this->assertEquals("foo", UriPath::part(0));
		$this->assertEquals("bar", UriPath::part(1));
		$this->assertEquals("choo", UriPath::part(2));
	}

	public function testUriPathCanReturnArray(){

		$_SERVER['REQUEST_URI'] = "/this/is/a/test";
		$this->assertEquals(array("this", "is", "a", "test"), UriPath::pathArray());
	}
}
