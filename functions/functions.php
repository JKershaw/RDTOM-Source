<?php
function save_log($log_name, $request_string, $question_ID = null)
{
	global $log_file_date_format, $mydb;
	
	// create the file name for the log
	$filename = "logs/" . date($log_file_date_format) . "_" . $log_name . ".txt";

	// add meta data to the string
	$stringData = date("[d-m-Y H:i:s]") . " [" . get_ip() . "] " . $request_string . "\n";
	
	// save the log
	file_put_contents($filename, $stringData, FILE_APPEND);  
	
	// if it's a report, also save it in the database
	if ($log_name == "report")
	{
		$report = new report(-1, get_ip(), gmmktime(), $question_ID, 0, $request_string, REPORT_OPEN);
		set_report($report);
	}

}

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
	unset($_SESSION['random_questions_asked']);
	unset($_SESSION['random_questions_results']);
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