<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('View the home page');
$I->amOnPage('');
$I->see('Roller Derby Test O\'Matic');
$I->see('Random Question');
?>