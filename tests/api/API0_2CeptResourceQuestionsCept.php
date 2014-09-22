<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test API 0.2 - Questions');

// Resource Questions
$I->amOnPage('/api/0.2/xml/questions/?developer=test&application=testingScript');
$I->see('<status_code>200</status_code>');

$I->amOnPage('/api/0.2/nicexml/questions/?developer=test&application=testingScript');
$I->see('&lt;api_version&gt;0.2&lt;/api_version&gt;');

$I->amOnPage('/api/0.2/json/questions/?developer=test&application=testingScript');
$I->see('"status_code":"200"');

$I->amOnPage('/api/0.2/jsonp/questions/?callback=my_function&developer=test&application=testingScript');
$I->see('"status_code":"200"');

// the search parameter
$I->amOnPage('/api/0.2/json/questions/?developer=test&application=testingScript&search=sausage');
$I->see('{"api_version":"0.2","status_code":"200","resource":{"questions":{"question":{"id":"704"');

?>