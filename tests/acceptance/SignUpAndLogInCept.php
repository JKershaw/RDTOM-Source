<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Sign up');
$I->amOnPage('');
$I->see('Log in or sign up');
$I->click('Log in or sign up');

//incorrect details
$I->see("Login to your account");
$I->fillField('name', 'testname');
$I->fillField('password', 'password');
$I->click('Login');
$I->see('Name and password combination not found, please try again.');
?>