<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw (Skate name: Sausage Roller, GitHub user: JKershaw)
 * 
 * Built to help Roller Derby players learn the rules
 */

try 
{
	// include needed files
	include('include.php');
	
	// start the session
	session_start();

	// create necessary objects & set up
	set_up_presentation();
	set_up_database();
	set_up_logged_in_user();
	set_up_url_array();
	
	// begin processing
	
	// do we want to perform some function or other, then show the default page?
	switch ($url_array[0]) 
	{	
		case "forget":
			forget_remebered_questions();
			break;	
		
		case "report":
			report_question();
			break;		
	}

	// show the page
	switch ($url_array[0]) 
	{	

		case "questions":
			include("presentation/allquestions.php");
			break;	
		case "poll":
			include("presentation/poll.php");
			break;	
		case "stats":
			include("presentation/stats.php");
			break;	
		case "admin":
			include("presentation/admin.php");
			break;	
		case "profile":
			include("presentation/profile.php");
			break;	
		case "passwordreset":
			include("presentation/passwordreset.php");
			break;	
		case "competition":
			include("presentation/competition.php");
			break;	
			
		case "question":
			set_up_question($url_array[1]);
			include("presentation/question.php");
			break;		
		default:
			set_up_question("random");
			include("presentation/question.php");
			break;
			
	}
} 
catch (Exception $e) 
{
	$log_error_string = 
		"MESSAGE: [" . $e->getMessage() . "] 
		URI: [" . $_SERVER['REQUEST_URI'] . "] 
		REQUEST: [" . print_r($_REQUEST, true) . "]";
	
	save_log("error", $log_error_string);
	
	$error_string =  $e->getMessage();
	include("presentation/error.php");
}
?>
