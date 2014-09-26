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
include ('include.php');

//start the session
session_start();

// start the output buffer
ob_start();

// create necessary objects & set up
set_up_presentation();
set_up_database();
set_up_logged_in_user();
set_up_url_array();

// begin processing the request

if (strtolower($url_array[0]) == "api") {
	
	// An API request
	include ('api/router.php');
} else {
	
	// A Web request
	
	// do we want to perform some function or other, then show the default page?
	switch ($url_array[0]) {
		case "report":
			report_question();
			break;
	}
	
	// show the page
	switch ($url_array[0]) {
		case "stats":
			include ("presentation/stats.php");
			break;

		case "admin":
			include ("presentation/admin.php");
			break;

		case "profile":
			include ("presentation/profile.php");
			break;

		case "passwordreset":
			include ("presentation/passwordreset.php");
			break;

		case "test":
			include ("presentation/test.php");
			break;

		case "about":
			include ("presentation/about.php");
			break;

		case "cat":
			include ("presentation/cat.php");
			break;

		case "forum":
			include ("presentation/forum.php");
			break;

		case "minimumskills":
			include ("presentation/minimumskills.php");
			break;

		case "question":
			set_up_question($url_array[1]);
			include ("presentation/question.php");
			break;

		case "forget":
			forget_remebered_questions();
			header('Location: ' . get_site_URL());
			die();
			break;

		default:
			set_up_question("random");
			include ("presentation/question.php");
			break;
	}
}

//Output the buffer
while (@ob_end_flush());
?>