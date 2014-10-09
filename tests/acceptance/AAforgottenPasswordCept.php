<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Get back a forgotten password');
$I->amOnPage('');
$I->click('Log in or sign up');
$I->click('Forgotten your password?');

$I->dontSee('For some reason the site has generated an error:');
