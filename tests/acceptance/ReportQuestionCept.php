<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Report a question');
$I->amOnPage('');
$I->see('Roller Derby Test O\'Matic');
$I->see('Random Question');

$I->click("Report this question");
$I->see('You should report a question if you think it\'s incorrect');
$I->fillField('#report_text', "This is my report, yo!");
$I->click('Submit Report');

$I->see('The anti-spam code wasn\'t entered correctly. Please try it again.');

$I->click('Report this question');
$I->see('You should report a question if you think it\'s incorrect');
$I->see('This is my report, yo!');
$I->fillField('#report_extra', "Derby");
$I->click('Submit Report');

$I->see('Your report has been filed');
