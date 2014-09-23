<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test public end points');

$I->amOnPage('/js/jquery_1_8_3.min.js');
$I->see("jQuery");

$I->amOnPage('/css/style.css');
$I->see("body");

?>