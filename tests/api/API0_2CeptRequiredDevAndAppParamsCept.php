<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test API 0.2 - Required parameters');

// The developer and application parameters
$I->amOnPage('/api/0.2/xml/question/');
$I->see('<status_code>400</status_code>');
$I->see('<status_code_text>Bad Request</status_code_text>');
$I->see('<status_message>Missing developer parameter.</status_message>');

$I->amOnPage('/api/0.2/xml/question/?developer=test');
$I->see('<status_code>400</status_code>');
$I->see('<status_code_text>Bad Request</status_code_text>');
$I->see('<status_message>Missing application parameter.</status_message>');

$I->amOnPage('/api/0.2/xml/question/?application=testingScript');
$I->see('<status_code>400</status_code>');
$I->see('<status_code_text>Bad Request</status_code_text>');
$I->see('<status_message>Missing developer parameter.</status_message>');

$I->amOnPage('/api/0.2/xml/question/?developer=test&application=testing.Script');
$I->see('<status_code>200</status_code>');

?>