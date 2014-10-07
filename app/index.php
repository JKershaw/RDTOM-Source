<?php

/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw (Skate name: Sausage Roller, GitHub user: JKershaw)
 *
 * Built to help Roller Derby players learn the rules
*/

// include needed files
include ('include.php');

// start the output buffer
ob_start();

// create necessary objects & set up
set_up_database();
set_up_logged_in_user();

// begin processing the request

if (UriPath::part(0) == "api") {
	// An API request
	include ('api/router.php');
} else {
	// A Web request
	switch (UriPath::part(0)) {
		case "forget":
			forget_remembered_questions();
			header('Location: ' . get_site_URL());
			die();
			break;
		case "report":
			report_question();
			set_up_question($_POST['report_question_ID']);
			include ("presentation/question.php");
			break;

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
			set_up_question(UriPath::part(1));
			include ("presentation/question.php");
			break;

		default:
			set_up_question("random");
			include ("presentation/question.php");
			break;
	}
}

//Output the buffer
while (@ob_end_flush());