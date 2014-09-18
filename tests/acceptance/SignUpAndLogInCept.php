<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Sign up');
$I->amOnPage('');
$I->see('Log in or sign up');
$I->click('Log in or sign up');

$I->amOnPage('/profile');
$I->see("Login to your account");
?>