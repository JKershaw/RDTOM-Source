<?php


function report_question()
{
	global $report_string, $recaptcha_privatekey, $error_string, $url_array;
	
	$resp = recaptcha_check_answer ($recaptcha_privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

	if ($_POST['report_question_ID'] && $resp->is_valid)
	{
		$report_string = "Question #" . $_POST['report_question_ID'] . ": " . $_POST['report_text'];
		save_log("report", $report_string, $_POST['report_question_ID']);
		
		// clear the input
		$_POST['report_text'] = false;
		$_POST['report_question_ID'] = false;
	} 
	else 
	{
		// Your code here to handle an error
		if (!$resp->is_valid)
		{
			$error_string = "The reCAPTCHA wasn't entered correctly. Please try it again.";
		}
		else
		{
			$error_string = "Sorry, and error has occured. Please try again";
		}
		
		// return to the question
		$url_array[0] = "question";
		$url_array[1] = $_POST['report_question_ID'];
	}
}


function forget_remebered_questions()
{
	delete_session('random_questions_asked');
	delete_session('random_questions_results');
}

function get_colour_from_percentage($perc_value)
{
	if ($perc_value >= 80)
	{
		$perc_colour = "#008000";
	}
	elseif ($perc_value >= 70)
	{
		$perc_colour = "#CC6600";
	}
	else
	{
		$perc_colour = "#FF0000";
	}
	
	return $perc_colour;
}

function compare_questions($req_question1, $req_question2)
{
	return strnatcasecmp($req_question1->get_Section(), $req_question2->get_Section());
}
?>