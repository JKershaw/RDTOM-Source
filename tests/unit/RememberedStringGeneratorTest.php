<?php

include __DIR__ . "/../../app/library/classes/presentation/ColourFromPercentageCalculator.class.php";

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
    
    public function testOneQuestionAnsweredCorrectly() {
        $ExpectedRememberedString = "You have a current success rate of 100% (1 correct out of 1). <a href=\"http://siteurl.com/forget\">Forget</a>";
        
        $questionsAnsweredResults = [true];
        
        $GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);
        
        $this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
    }
    
    public function testTwoQuestionsAnsweredCorrectly() {
        $ExpectedRememberedString = "You have a current success rate of 100% (2 correct out of 2). <a href=\"http://siteurl.com/forget\">Forget</a>";
        
        $questionsAnsweredResults = [true, true];
        
        $GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);
        
        $this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
    }
}

class RememberedStringGenerator
{
    private $site_url;

    function __construct($site_url) {
        $this->site_url = $site_url;
    }
    
    public function generate($questionsAnsweredResults) {
        
        $questionsAnswered = count($questionsAnsweredResults);
        
        if ($questionsAnswered <= 0) {
            return "You've not answered any questions recently.";
        }
        
        return "You have a current success rate of 100% (" . $questionsAnswered . " correct out of " . $questionsAnswered . "). <a href=\"" . $this->site_url . "forget\">Forget</a>";
    }
}
