<?php
include_once (__DIR__ . "/../../app/library/classes/presentation/RememberedStringGenerator.class.php");

class RememberedStringGeneratorTest extends \PHPUnit_Framework_TestCase
{
	private $RememberedStringGenerator;
	
	protected function setUp() {
		$site_url = "http://siteurl.com/";
		$this->RememberedStringGenerator = new RememberedStringGenerator($site_url);
	}
	
	public function testNoQuestionsAnswered() {
		$ExpectedRememberedString = "You've not answered any questions recently.";
		
		$questionsAnsweredResults = null;
		
		$GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);
		
		$this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
	}
	
	public function testQuestionsAnsweredCorrectly() {
		$ExpectedRememberedString = "You have a current success rate of <span style=\"font-weight:bold; color:#008000\">100%</span> (1 correct out of 1). <a href=\"http://siteurl.com/forget\">Forget</a>";
		
		$questionsAnsweredResults = [true];
		
		$GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);
		
		$this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
		
		$ExpectedRememberedString = "You have a current success rate of <span style=\"font-weight:bold; color:#008000\">100%</span> (2 correct out of 2). <a href=\"http://siteurl.com/forget\">Forget</a>";
		
		$questionsAnsweredResults = [true, true];
		
		$GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);
		
		$this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
	}
	
	public function testQuestionsAnsweredDifferently() {
		$ExpectedRememberedString = "You have a current success rate of <span style=\"font-weight:bold; color:#FF0000\">50%</span> (1 correct out of 2). <a href=\"http://siteurl.com/forget\">Forget</a>";
		$questionsAnsweredResults = [true, false];
		$GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);
		$this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
		
		
		$ExpectedRememberedString = "You have a current success rate of <span style=\"font-weight:bold; color:#FF0000\">50%</span> (2 correct out of 4). <a href=\"http://siteurl.com/forget\">Forget</a>";
		$questionsAnsweredResults = [true, true, false, false];
		$GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);
		$this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
		
		
		$ExpectedRememberedString = "You have a current success rate of <span style=\"font-weight:bold; color:#CC6600\">75%</span> (3 correct out of 4). <a href=\"http://siteurl.com/forget\">Forget</a>";
		$questionsAnsweredResults = [true, true, true, false];
		$GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);
		$this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
				
	}
}
