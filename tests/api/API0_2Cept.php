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
$I->see('{"api_version":"0.2","status_code":"200","resource":{"question":{"id":"704",');

$I->amOnPage('/api/0.2/jsonp/question/?developer=test&application=testingScript');
$I->see('<status_message>No JSONP callback specified</status_message>');

$I->amOnPage('/api/0.2/jsonp/question/?callback=my_function&developer=test&application=testingScript');
$I->see('"status_code":"200"');

$I->amOnPage('/api/0.2/jsonp/question/?callback=my_function&ID=704&developer=test&application=testingScript');
$I->see('my_function ({"api_version":"0.2","status_code":"200","resource":{"question":{"id":"704",');

$I->amOnPage('/api/0.2/JavaScript/question/?ID=704&developer=test&application=testingScript');
$I->see('<status_code>0</status_code>');
$I->see('<status_message>No Javascript variable name specified</status_message>');

$I->amOnPage('/api/0.2/JavaScript/question/?ID=704&developer=test&application=testingScript&var=var');
$I->see('var var = {"api_version":"0.2","status_code":"200","resource"');

// Resource Questions

// Resource Statistic 

// Resource Changes


?>