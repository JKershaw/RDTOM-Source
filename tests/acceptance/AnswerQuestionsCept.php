<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Answer a question correctly');
$I->amOnPage('');
$I->see('Roller Derby Test O\'Matic');
$I->see('Random Question');

// Answer a question correctly
$I->dontSee('You have a current success rate of 100% (1 correct out of 1)', "#remebered_string_p");
$I->dontSee('You Win!');

$I->click(".correct_answer_link");
$I->waitForElementChange('#remebered_string', function(\WebDriverElement $el) {
    return $el->isDisplayed();
}, 100);

$I->see('You have a current success rate of 100% (1 correct out of 1)', "#remebered_string_p");
$I->see('You Win!', ".correct_answer_win");

// Answer another question, this time wrong
$I->wantTo('Answer a question wrong');
$I->click("New Question");
$I->see('You have a current success rate of 100% (1 correct out of 1)', "#remebered_string_p");
$I->dontSee('Wrong!');

$I->click(".wrong_answer_link");
$I->waitForElementChange('#remebered_string', function(\WebDriverElement $el) {
    return $el->isDisplayed();
}, 100);

$I->see('You have a current success rate of 50% (1 correct out of 2)', "#remebered_string_p");
$I->see('Wrong!', ".wrong_answer");

// Get a streak going
$I->click("New Question");
$I->click(".correct_answer_link");
$I->waitForElementChange('#remebered_string', function(\WebDriverElement $el) {
    return $el->isDisplayed();
}, 100);
$I->click("New Question");
$I->click(".correct_answer_link");
$I->waitForElementChange('#remebered_string', function(\WebDriverElement $el) {
    return $el->isDisplayed();
}, 100);
$I->click("New Question");
$I->click(".correct_answer_link");
$I->waitForElementChange('#remebered_string', function(\WebDriverElement $el) {
    return $el->isDisplayed();
}, 100);
$I->click("New Question");
$I->click(".correct_answer_link");
$I->waitForElementChange('#remebered_string', function(\WebDriverElement $el) {
    return $el->isDisplayed();
}, 100);
$I->click("New Question");
$I->click(".correct_answer_link");
$I->waitForElementChange('#remebered_string', function(\WebDriverElement $el) {
    return $el->isDisplayed();
}, 100);

$I->see('You are on a winning streak of 5', "#remebered_string_p");
$I->see('You have a current success rate of 85.71% (6 correct out of 7)', "#remebered_string_p");

// Break the streak
$I->click("New Question");
$I->click(".wrong_answer_link");
$I->waitForElementChange('#remebered_string', function(\WebDriverElement $el) {
    return $el->isDisplayed();
}, 100);
$I->wait(2);

$I->see('You just ended your streak of 5', "#remebered_string_p");
$I->see('You have a current success rate of 75% (6 correct out of 8)', "#remebered_string_p");

// Forget and start again
$I->click("Forget");

$I->dontSee('You have a current success rate of', "#remebered_string_p");
$I->dontSee('You Win!');

$I->click(".correct_answer_link");

$I->see('You have a current success rate of 100% (1 correct out of 1)', "#remebered_string_p");
$I->see('You Win!', ".correct_answer_win");
?>