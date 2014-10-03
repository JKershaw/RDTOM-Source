<?php
function report_question() {
	if (!$_POST) {
		header('Location: ' . get_site_URL());
		exit;
	}
	
	global $report_string, $error_string, $url_array;
	
	if ($_POST['report_question_ID'] && (strtolower(trim($_POST['report_extra'])) == "derby")) {
		$report_string = "Question #" . $_POST['report_question_ID'] . ": " . $_POST['report_text'];
		save_log("report", $report_string, $_POST['report_question_ID']);
		
		// clear the input
		$_POST['report_text'] = false;
		$_POST['report_question_ID'] = false;
	} else {
		
		// Your code here to handle an error
		if (!(strtolower(trim($_POST['report_extra'])) == "derby")) {
			$error_string = "The anti-spam code wasn't entered correctly. Please try it again.";
		} else {
			$error_string = "Sorry, and error has occured. Please try again";
		}
		
		// return to the question
		$url_array[0] = "question";
		$url_array[1] = $_POST['report_question_ID'];
	}
}

function forget_remebered_questions() {
	$session = new Session();
	$session->forget("random_questions_results");
	$session->forget("random_questions_asked");
}

function compare_questions($req_question1, $req_question2) {
	return strnatcasecmp($req_question1->get_Section() , $req_question2->get_Section());
}