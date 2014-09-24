<?php

class RememberedStringGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testNoQuestionsAnswered()
    {
        $RememberedStringGenerator = new RememberedStringGenerator();
        $ExpectedRememberedString = "You've not answered any questions recently.";

        $questionsAnsweredResults = null;

        $GeneratedRememberedString = $RememberedStringGenerator->generate($questionsAnsweredResults);

        $this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
    }

    public function testOneQuestionAnsweredCorrectly()
    {
        $RememberedStringGenerator = new RememberedStringGenerator();
        $ExpectedRememberedString = "You have a current success rate of 100% (1 correct out of 1). Forget";

        $questionsAnsweredResults = [true];

        $GeneratedRememberedString = $RememberedStringGenerator->generate($questionsAnsweredResults);

        $this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
    }

}

class RememberedStringGenerator
{
    // method declaration
    public function generate($questionsAnsweredResults) {

        if (count($questionsAnsweredResults) > 0) {
            return "You have a current success rate of 100% (1 correct out of 1). Forget";
        } else {
            return "You've not answered any questions recently.";
        }
    }
}