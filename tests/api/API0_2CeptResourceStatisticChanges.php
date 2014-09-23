<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test API 0.2 - Changes');

$I->amOnPage('/api/0.2/xml/changes/?developer=SausageRoller&application=testing');
$I->see('<status_code>400</status_code>');
$I->see('You must supply a GM timestamp in the since perameter');

$I->amOnPage('/api/0.2/xml/changes/?developer=SausageRoller&application=testing&since=1369819830');
$I->see('<status_code>200</status_code>');

?>