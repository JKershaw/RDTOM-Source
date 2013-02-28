<?php 
/*
 * All files we need to include
 */

// Exception handling
include('functions/exceptions.php');

//configuration files
include('config.php');

// Control (function) files 
include('functions/functions.php');
include('functions/presentation.fnc.php');
include('functions/support.fnc.php');
include('functions/control.fnc.php');
include('functions/account.fnc.php');
include('functions/stats.fnc.php');
include('functions/email.fnc.php');
include('functions/tracker.fnc.php');
include('functions/cache.fnc.php');
include('functions/sessions.fnc.php');

// Database (function) files
include('functions/database/questions.db.fnc.php');
include('functions/database/answers.db.fnc.php');
include('functions/database/reports.db.fnc.php');
include('functions/database/comments.db.fnc.php');

// Model (object) files autoload, phpMailer is also Autoloaded as used rarely
function __autoload($classname) 
{
	switch (strtolower($classname)) 
	{	
		case "phpmailer":
			$filename = "functions/phpmailer/class.phpmailer.php";
			break;	
		
		default:
			$filename = "objects/". $classname .".obj.php";
			break;		
	}	
    include($filename);
}

?>