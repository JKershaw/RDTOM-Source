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

