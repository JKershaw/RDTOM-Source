<?php 
/*
 * All files we need to include
 */

// Exception handling
include('functions/exceptions.php');

// include needed files
include('config.php');

// Control (function) files 
include('functions/functions.php');
include('functions/presentation.fnc.php');
include('functions/support.fnc.php');
include('functions/control.fnc.php');
include('functions/account.fnc.php');
include('functions/stats.fnc.php');
include('functions/email.fnc.php');

// Database (function) files
include('functions/database/questions.db.fnc.php');
include('functions/database/answers.db.fnc.php');
include('functions/database/reports.db.fnc.php');

// Model (object) files
include('objects/database_derbytest.obj.php');
include('objects/question.obj.php');
include('objects/answer.obj.php');
include('objects/response.obj.php');
include('objects/report.obj.php');
include('objects/user.obj.php');
include('objects/term.obj.php');

// Extra files
include("functions/phpmailer/class.phpmailer.php");
include('functions/recaptcha/recaptchalib.php');

?>