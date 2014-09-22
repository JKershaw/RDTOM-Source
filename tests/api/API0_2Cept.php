<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test version 0.2 of the API');

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


// Resource Question
$I->amOnPage('/api/0.2/xml/question/?developer=test&application=testingScript');
$I->see('<status_code>200</status_code>');

$I->amOnPage('/api/0.2/xml/question/?ID=704&developer=test&application=testingScript');
$I->see('<status_code>200</status_code>');
$I->see('Sausage Roller');

$I->amOnPage('/api/0.2/nicexml/question/?developer=test&application=testingScript');
$I->see('&lt;api_version&gt;0.2&lt;/api_version&gt;');

$I->amOnPage('/api/0.2/json/question/?developer=test&application=testingScript');
$I->see('"status_code":"200"');

$I->amOnPage('/api/0.2/json/question/?ID=704&developer=test&application=testingScript');
$I->see('{"api_version":"0.2","status_code":"200","resource":{"question":{"id":"704","text":"True or False: \"Sausage Roller\" is a really clever and funny name for a Merby (Men\'s Derby) skater.","wftda_link":{},"terms":{"language":"English"},"notes":"NB: A \"sausage roll\" is a tasty snack popular in the UK. It\'s sausage meat rolled and baked in pastry. Yum.","sections":{"section":"N\/A"},"answers":{"answer":[{"id":"3052","text":"False","correct":"false"},{"id":"3051","text":"True","correct":"true"}]}}}}');

$I->amOnPage('/api/0.2/jsonp/question/?developer=test&application=testingScript');
$I->see('<status_message>No JSONP callback specified</status_message>');

$I->amOnPage('/api/0.2/jsonp/question/?callback=my_function&developer=test&application=testingScript');
$I->see('"status_code":"200"');

$I->amOnPage('/api/0.2/jsonp/question/?callback=my_function&ID=704&developer=test&application=testingScript');
$I->see('my_function ({"api_version":"0.2","status_code":"200","resource":{"question":{"id":"704","text":"True or False: \"Sausage Roller\" is a really clever and funny name for a Merby (Men\'s Derby) skater.","wftda_link":{},"terms":{"language":"English"},"notes":"NB: A \"sausage roll\" is a tasty snack popular in the UK. It\'s sausage meat rolled and baked in pastry. Yum.","sections":{"section":"N\/A"},"answers":{"answer":[{"id":"3052","text":"False","correct":"false"},{"id":"3051","text":"True","correct":"true"}]}}}}, \'\');');


// // Resource Questions
// $I->amOnPage('/api/0.1/xml/questions/');
// $I->see('<status_code>200</status_code>');

// $I->amOnPage('/api/0.1/nicexml/questions/');
// $I->see('&lt;api_version&gt;0.1&lt;/api_version&gt;');

// $I->amOnPage('/api/0.1/json/questions/');
// $I->see('"status_code":"200"');

// $I->amOnPage('/api/0.1/jsonp/questions/?callback=my_function');
// $I->see('"status_code":"200"');

// ?>