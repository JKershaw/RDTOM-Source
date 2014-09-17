<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Sign up, log out, then log in again');
$I->amOnPage('');
$I->seeLink('Log in or sign up');
$I->click('Log in or sign up');

$I->amOnPage('/profile');
$I->fillField('name', 'Test User Who Does not exist');
$I->fillField('password', 'Test Password');
$I->click('Login');

$I->see("Name and password combination not found", ".error_string");
?>