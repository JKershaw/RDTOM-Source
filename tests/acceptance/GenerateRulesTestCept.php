<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Generate a Rules test');
$I->amOnPage('');
$I->see('Generate a Rules Test');
$I->click('Generate a Rules Test');

$I->amOnPage('/test');
$I->see("Click the button to randomly generate an online rules test");

$I->click("Generate Rules Test");

//$I->amOnPage('/test/generate/?d=wftda&n=45&p=80&o=interactiveHTML');
$I->see("Pass mark");
$I->click("I've finished! Mark my test, please.");
$I->see("FAIL :(");
?>