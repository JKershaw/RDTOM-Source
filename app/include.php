<?php

/*
 * All files we need to include
*/

// Exception handling
include ('library/exceptions.php');

//configuration files
include ('config.php');

// Library files
include ('library/functions.php');
include ('library/presentation.fnc.php');
include ('library/support.fnc.php');
include ('library/control.fnc.php');
include ('library/account.fnc.php');
include ('library/stats.fnc.php');
include ('library/email.fnc.php');
include ('library/tracker.fnc.php');
include ('library/cache.fnc.php');
include ('library/sessions.fnc.php');
include ('library/forum.fnc.php');

// Data mapping files
include ('library/mappers/database.obj.php');
include ('library/mappers/database_derbytest.obj.php');
include ('library/mappers/questions.db.fnc.php');
include ('library/mappers/answers.db.fnc.php');
include ('library/mappers/reports.db.fnc.php');
include ('library/mappers/comments.db.fnc.php');
include ('library/mappers/test.db.fnc.php');
include ('library/mappers/test_ratings.db.fnc.php');

// Model (object) files autoload, phpMailer is also Autoloaded as used rarely

$classesDir = array(
	__DIR__ . '/library/classes/presentation/',
	__DIR__ . '/library/classes/storage/'
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
	
	$classname = preg_replace("/[^a-z_]/", '', strtolower($classname));
	$filename = "objects/" . $classname . ".class.php";
	
	include_once ($filename);
}
?>