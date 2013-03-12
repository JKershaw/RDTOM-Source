<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw (Skate name: Sausage Roller, GitHub user: JKershaw)
 * 
 * Built to help Roller Derby players learn the rules
 */

// start the page speed tracker
list($usec, $sec) = explode(" ", microtime());
$page_timer_start = ((float)$usec + (float)$sec);

// include needed files
include('include.php');

// start the output buffer
ob_start ();

// create necessary objects & set up
set_up_presentation();
set_up_database();
set_up_logged_in_user();
set_up_url_array();

// begin processing the request

if (strtolower($url_array[0]) == "api")
{
	// An API request
	include('api/router.php');
}
else
{	
	// A Web request
	
	// do we want to perform some function or other, then show the default page?
	switch ($url_array[0]) 
	{	
		case "forget":
			forget_remebered_questions();
			break;	
			
		case "changes":
			toggle_view_only_changes();
			break;	
		
		case "report":
			report_question();
			break;		
	}
	
	// show the page
	switch ($url_array[0]) 
	{	
	
		case "questions":
			include("presentation/questions.php");
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
		case "test":
			include("presentation/test.php");
			break;	
		case "submit":
			include("presentation/submit.php");
			break;	
		case "about":
			include("presentation/about.php");
			break;	
		case "cat":
			include("presentation/cat.php");
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
//Output the buffer
while (@ob_end_flush());

?>