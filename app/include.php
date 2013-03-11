<?php 
/*
 * All files we need to include
 */

// Exception handling
include('library/exceptions.php');

//configuration files
include('config.php');

// Library files 
include('library/functions.php');
include('library/presentation.fnc.php');
include('library/support.fnc.php');
include('library/control.fnc.php');
include('library/account.fnc.php');
include('library/stats.fnc.php');
include('library/email.fnc.php');
include('library/tracker.fnc.php');
include('library/cache.fnc.php');
include('library/sessions.fnc.php');

// Data mapping files
include('library/mappers/database.obj.php');
include('library/mappers/database_derbytest.obj.php');
include('library/mappers/questions.db.fnc.php');
include('library/mappers/answers.db.fnc.php');
include('library/mappers/reports.db.fnc.php');
include('library/mappers/comments.db.fnc.php');

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