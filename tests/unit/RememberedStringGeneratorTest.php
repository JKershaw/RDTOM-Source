<?php

class RememberedStringGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $RememberedStringGenerator;

    protected function setUp()
    {
        $this->RememberedStringGenerator = new RememberedStringGenerator();
    }

    public function testNoQuestionsAnswered()
    {
        $ExpectedRememberedString = "You've not answered any questions recently.";

        $questionsAnsweredResults = null;

        $GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);

        $this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
    }

    public function testOneQuestionAnsweredCorrectly()
    {
        $ExpectedRememberedString = "You have a current success rate of 100% (1 correct out of 1). Forget";

        $questionsAnsweredResults = [true];

        $GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);

        $this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
    }

    public function testTwoQuestionsAnsweredCorrectly()
    {
        $ExpectedRememberedString = "You have a current success rate of 100% (2 correct out of 2). Forget";

        $questionsAnsweredResults = [true, true];

        $GeneratedRememberedString = $this->RememberedStringGenerator->generate($questionsAnsweredResults);

        $this->assertEquals($GeneratedRememberedString, $ExpectedRememberedString);
    }

}

class RememberedStringGenerator
{
    // method declaration
    public function generate($questionsAnsweredResults) {

        if (count($questionsAnsweredResults) == 1) {
            return "You have a current success rate of 100% (1 correct out of 1). Forget";
        } elseif (count($questionsAnsweredResults) == 2) {
            return "You have a current success rate of 100% (2 correct out of 2). Forget";
        } else {
            return "You've not answered any questions recently.";
        }
    }
}