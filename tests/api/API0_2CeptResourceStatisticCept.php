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

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=api_calls_hourly');
$I->see('<status_code>200</status_code>');
$I->see('<id>api_calls_hourly</id>');

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=responses_daily');
$I->see('<status_code>200</status_code>');
$I->see('<id>responses_daily</id>');

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=responses_hourly');
$I->see('<status_code>200</status_code>');
$I->see('<id>responses_hourly</id>');

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=responses_minutly');
$I->see('<status_code>200</status_code>');
$I->see('<id>responses_minutly</id>');

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=questions');
$I->see('<status_code>200</status_code>');
$I->see('<id>questions</id>');

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=answers');
$I->see('<status_code>200</status_code>');
$I->see('<id>answers</id>');

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=users');
$I->see('<status_code>200</status_code>');
$I->see('<id>users</id>');

$I->amOnPage('/api/0.2/xml/Statistic/?developer=test&application=testingScript&search=sausage&ID=unique_IPs');
$I->see('<status_code>200</status_code>');
$I->see('<id>unique_IPs</id>');

?>