<?php
function report_question() {
	if (!$_POST) {
		header('Location: ' . get_site_URL());
		exit;
	}
	
	global $reportHasBeenFiled, $error_string;

	$questionID = $_POST['report_question_ID'];
	
	if ($questionID && (strtolower(trim($_POST['report_extra'])) == "derby")) {

		$report_string = "Question #" . $questionID . ": " . $_POST['report_text'];

		save_log("report", $report_string, $questionID);

		$report = new report(-1, get_ip() , gmmktime() , $questionID, 0, $report_string, REPORT_OPEN);
		set_report($report);

		// clear the input
		$_POST['report_text'] = false;
		$questionID = false;

		$reportHasBeenFiled = true;
	} else {
		
		// Your code here to handle an error
		if (!(strtolower(trim($_POST['report_extra'])) == "derby")) {
			$error_string = "The anti-spam code wasn't entered correctly. Please try it again. | " . $_POST['report_extra'];
		} else {
			$error_string = "Sorry, and error has occured. Please try again";
		}
	}
}