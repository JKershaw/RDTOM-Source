<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test API 0.2 - Statistic');

// Resource Questions
$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage');
$I->see('<status_code>416</status_code>');
$I->see('Statistic ID not found');

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=responses');
$I->see('<status_code>200</status_code>');
$I->see('<id>responses</id>');


//responses, api_calls_hourly, responses_daily, responses_hourly, responses_minutly, questions, answers, users, unique_IPs
?>