<?php
// these functions, when called on, will load data and set up global variables

function set_up_question($question_ID)
{
	global $mydb, $question, $answers, $correct_answer, $is_question, $is_random_question;
	
	// set the is question global variable
	$is_question = true;

	if (($question_ID == "random") || !$question_ID)
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
	
	// get the correct answer and remember it
	foreach ($answers as $answer)
	{
		if ($answer->is_correct())
		{
			$correct_answer = $answer;
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
	$myPDO = new PDO("mysql:dbname=" . DATABASE_NAME . ";host=" . DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD);

}

function set_up_url_array()
{
	global $url_array;
	
	// get the URL components
	foreach (explode("/", $_SERVER['REQUEST_URI']) as $segment)
	{
		if (trim($segment))
		{
			$url_array[] = strtolower($segment);
		}
	}
}