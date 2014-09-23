<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Sign up');
$I->amOnPage('');
$I->see('Log in or sign up');
$I->click('Log in or sign up');

//incorrect details
$I->see("Login to your account");
$I->dontSee("If you already have an account");
$I->fillField('name', 'testname');
$I->fillField('password', 'password');
$I->click('Login');
$I->see('Name and password combination not found, please try again.');

// sign up
$I->wait(10);
$I->click('click here to get one');
$I->see("Sign up");
$I->dontSee("Login to your account");
$I->fillField('#signup_name', 'testname');
$I->fillField('#signup_password', 'password');
$I->click('Sign up');
$I->see('Your account has been made, please log in now ');

// sign in
$I->see("Login to your account");
$I->fillField('name', 'testname');
$I->fillField('password', 'password');
$I->click('Login');

// I'm now logged in
$I->see('You have not answered any questions whilst logged in');



?>