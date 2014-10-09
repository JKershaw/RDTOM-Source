<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Build my own test');
$I->amOnPage('');

// sign up

$I->click('Log in or sign up');
$I->click('click here to get one');
$I->see("Sign up");
$I->dontSee("Login to your account");
$I->fillField('#signup_name', "TestingCustomTest");
$I->fillField('#signup_password', "123password");
$I->click('Sign up');


// build test
$I->see('Build your own Test');
$I->click('Build your own Test');

$I->click('Make a new test');

$I->click('Beginner');

$I->waitForElement('.ui-state-default', 30);
$I->click('Add to test');

$I->fillField("test_title", "The title of my test");


$I->dontSee("Last saved at");
$I->click("Save");
$I->waitForText('Last saved at', 30);
$I->see("Last saved at");

$I->click("Back to tests overview");
$I->see("The title of my test");