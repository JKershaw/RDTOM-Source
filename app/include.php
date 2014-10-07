<?php

/*
 * All files we need to include
*/

// Exception handling
include ('library/exceptions.php');

//configuration files
include ('config.php');

// Library files
include ('library/presentation.fnc.php');
include ('library/control.fnc.php');
include ('library/account.fnc.php');
include ('library/stats.fnc.php');
include ('library/forum.fnc.php');
include ('library/admin.fnc.php');
include ('library/charts.fnc.php');
include ('library/url.fnc.php');
include ('library/report.fnc.php');

include ('library/ajax.fnc.php');
include ('library/cron.fnc.php');

// Data mapping files
include ('library/mappers/database.obj.php');
include ('library/mappers/database_derbytest.obj.php');
include ('library/mappers/questions.db.fnc.php');
include ('library/mappers/answers.db.fnc.php');
include ('library/mappers/reports.db.fnc.php');
include ('library/mappers/comments.db.fnc.php');
include ('library/mappers/test.db.fnc.php');
include ('library/mappers/test_ratings.db.fnc.php');

$classesDir = array(
	__DIR__ . '/library/classes/presentation/',
	__DIR__ . '/library/classes/storage/',
	__DIR__ . '/library/classes/email/',
	__DIR__ . '/library/classes/account/',
	__DIR__ . '/library/classes/misc/',
	__DIR__ . '/objects/'
);

function __autoload($classname) {
	
	if ($classname == "PHPMailer") {
		include_once ("library/phpmailer/class.phpmailer.php");
		return;
	}
	
	if (substr($classname, 0, 4) == "api_") {
		include_once (api_resources_autoload($classname));
		return;
	}
	
	global $classesDir;
	foreach ($classesDir as $directory) {
		if (file_exists($directory . $classname . '.class.php')) {
			require_once ($directory . $classname . '.class.php');
			return;
		}
	}
}
?>