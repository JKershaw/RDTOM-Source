<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Answer some questions');
$I->amOnPage('');
$I->see('Roller Derby Test O\'Matic');
$I->see('Random Question');

// Answer a question correctly

// Answer a question wrong

// Get a streak going

// Break the streak
?>