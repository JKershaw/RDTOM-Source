<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('View the stats page');
$I->amOnPage('');
$I->click('Site stats');
$I->see('General Stats:');

$I->wantTo('View the about page');
$I->click('About, Disclaimer & Privacy Policy');
$I->see('About RDTOM');

$I->wantTo('View the cat page');
$I->click('Meow');
$I->see('Answer questions and get a happy cat if you\'re right. A sad cat if you\'re wrong.');