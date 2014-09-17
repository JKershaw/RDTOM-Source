<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Sign up, log out, then log in again');
$I->amOnPage('');
$I->seeLink('Log in or sign up');
$I->click('Log in or sign up');

$I->amOnPage('/profile');
$I->see("Login to your account");
?>