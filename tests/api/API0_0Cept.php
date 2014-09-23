<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test version 0.0 of the API');
$I->amOnPage('/api/0.0/xml/question/');
$I->see('<status_code>601</status_code>');



$I->amOnPage('/js/jquery_1_8_3.min.js');
?>