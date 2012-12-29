<?php
// these functions, when called on, will load data and set up global variables

function set_up_question($question_ID)
{
	global $mydb, $question, $answers, $correct_answer, $is_question, $is_random_question;
	
	// set the is question global variable
	$is_question = true;

	if ($question_ID == "random")
	{
		$is_random_question = true;
		$question = get_question_random();
	}
	else
	{
		$is_random_question = false;
		$question = get_question_from_ID($question_ID);

	}
	
	// get random answers
	$answers = $question->get_Answers();
	
	// get the correct answer and remeber it
	foreach ($answers as $answer)
	{
		if ($answer->is_correct())
		{
			$correct_answer = $answer;
		}
	}
	
	if (!$correct_answer)
	{
		throw new exception("For some reason no correct answer could be found for question #" . $question->get_ID());
	}
}

function set_up_logged_in_user()
{
	global $mydb, $user;
	
	// do we have a session variable?
	if ($_SESSION['rdtom_userID'])
	{
		$user = $mydb->get_user_from_ID($_SESSION['rdtom_userID']);
	}
	elseif ($_COOKIE["token"])
	{
		// is it valid?
		$tmp_user = $mydb->get_user_from_token($_COOKIE["token"], get_ip());
		if ($tmp_user)
		{
			// we have a valid token, so remeber the user
			$user = $tmp_user;
			$_SESSION['rdtom_userID'] = $user->get_ID();
		}
	}
}

function set_up_database()
{
	// set up the mysql Object
	global $mydb;
	$mydb = new database_derbytest();
	
	// set up the PDO object
	global $myPDO;
	global $database_username, $database_userpassword, $database_name, $database_host;
	
	/* Connect to an ODBC database using driver invocation */
	try 
	{
	    $myPDO = new LoggedPDO("mysql:dbname=$database_name;host=$database_host", $database_username, $database_userpassword);
	} 
	catch (PDOException $e) 
	{
	    die('Connection failed: ' . $e->getMessage());
	}
}

function set_up_url_array()
{
	global $url_array;
	
	// get the URL components
	foreach (explode("/", $_SERVER['REQUEST_URI']) as $segment)
	{
		//if (trim($segment) && (substr($segment, 0, 1) != "?"))
		if (trim($segment))
		{
			//$url_array[] = preg_replace("/[^%a-zA-Z0-9-_']/", "", $segment);
			$url_array[] = $segment;
		}
	}
	
	// Backwards compatability for URLs
	// ?forget=yes
	// ?report=yes
	// all the .php files; stats.php, poll.php, allquestions.php
	// ?question=123
	// allquestions.php?hard=yes
	// allquestions.php?easy=yes
	
	if ($url_array[0] == "?forget=yes")
	{
		$url_array[0] = "forget";
	}
	elseif ($url_array[0] == "?report=yes")
	{
		$url_array[0] = "report";
	}
	elseif ($url_array[0] == "stats.php")
	{
		$url_array[0] = "stats";
	}
	elseif ($url_array[0] == "poll.php")
	{
		$url_array[0] = "poll";
	}
	elseif ($url_array[0] == "allquestions.php")
	{
		$url_array[0] = "questions";
	}
	elseif ($url_array[0] == "allquestions.php?hard=yes")
	{
		$url_array[0] = "questions";
		$url_array[1] = "hard";
	}
	elseif ($url_array[0] == "allquestions.php?easy=yes")
	{
		$url_array[0] = "questions";
		$url_array[1] = "easy";
	}
	elseif (strstr($url_array[0], "?question="))
	{
		$url_array[1] = str_replace("?question=", "", $url_array[0]);
		$url_array[0] = "question";
	}
	
	// if there is a request for a question, but no number is given, make the request for a random question
	if ($url_array[0] == "question" && !($url_array[1] > 0))
	{
		$url_array[0] = "";
	}
}

function set_up_presentation()
{
	global $is_question, $is_random_question;
	// Global variables to help with the presentation
	$is_question = false;
	$is_random_question = false;
}
?>