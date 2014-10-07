<?php

function set_up_presentation()
{
	global $is_question, $is_random_question;
	// Global variables to help with the presentation
	$is_question = false;
	$is_random_question = false;
}

function is_logged_in() {
	$user = get_global_user();
	if ($user) {
		return true;
	}
	
	return false;
}

function is_question() {
	global $is_question, $question;
	return $is_question && $question;
}

function is_random_question() {
	global $is_question, $is_random_question, $question;
	return $is_question && $question && $is_random_question;
}

function set_page_title($req_title) {
	global $page_title;
	$page_title = $req_title;
}

function get_page_title() {
	global $page_title;
	if ($page_title) {
		return $page_title;
	} else {
		return "Roller Derby Test O'Matic";
	}
}

function get_page_description() {
	global $question;
	
	if (!is_random_question() && is_question()) {
		$out = htmlspecialchars(stripslashes("Queston #" . $question->get_ID() . " - " . $question->get_Text()));
	} else {
		$out = "A free online WFTDA roller derby rules test with hundreds of questions. Turn left and learn the rules.";
	}
	return $out;
}

// set functions
function set_page_subtitle($req_subtitle) {
	global $page_subtitle;
	$page_subtitle = $req_subtitle;
}

// get functions
function get_page_subtitle() {
	global $page_subtitle;
	if ($page_subtitle) {
		return $page_subtitle;
	} else {
		return "Turn left and learn the rules.";
	}
}

function get_remembered_string() {
	$session = new Session();
	$siteUrl = get_site_URL();
	$questionsAnsweredResults = $session->get('random_questions_results');
	
	$RememberedStringGenerator = new RememberedStringGenerator($siteUrl);
	$generatedRememberedString = $RememberedStringGenerator->generate($questionsAnsweredResults);
	
	return $generatedRememberedString;
}

function get_CSS_embed($type = false) {
	if ($type && $type == "print") {
		return "
		    <style type=\"text/css\">
		        @media print {
					body	
					{
						font-size:12px;
						max-width: 100%;	
					}
					
					.footer
					{
						display:none;
					}
					
					
					.print_footer 
					{
						display: block;
					}
		        }
		    </style>
			";
	}
	return "<link rel=\"stylesheet\" href=\"" . get_site_URL(true) . "css/style.css?v=" . filemtime("css/style.css") . "\" type=\"text/css\" >";
}

function get_error_string($nice_formatting = true) {
	global $error_string;
	if ($nice_formatting) {
		return htmlentities($error_string);
	} else {
		return $error_string;
	}
}

function get_recent_wrong_questions() {
	global $user, $mydb;
	
	// get all the incorrect responses from the past 2 weeks
	
	$questions = get_questions_from_User_ID($user->get_ID() , 20, 1209600, true);
	
	if ($questions) {
		$out.= "<h3>Recent questions you got wrong</h3>";
		$out.= "<p class=\"small_p\">";
		foreach ($questions as $question) {
			$question_string = $question->get_Text();
			if (strlen($question_string) > 100) {
				$question_string = substr($question_string, 0, 97) . "...";
			}
			
			$out.= $question->get_Section() . " <a href=\"" . $question->get_URL() . "\">" . htmlentities(stripslashes($question_string)) . "</a><br />";
		}
		$out.= "</p>";
	}
	
	return $out;
}

function get_recent_questions() {
	global $user, $mydb;
	
	// get all the  responses from the past 24 hours
	
	$questions = get_questions_from_User_ID($user->get_ID() , 20, 86400, false);
	
	$out.= "<h3>All the questions you've answered in the past 24 hours</h3>";
	
	if ($questions) {
		$out.= "<p class=\"small_p\">";
		foreach ($questions as $question) {
			$question_string = $question->get_Text();
			if (strlen($question_string) > 100) {
				$question_string = substr($question_string, 0, 97) . "...";
			}
			
			$out.= $question->get_Section() . " <a href=\"" . $question->get_URL() . "\">" . htmlentities(stripslashes($question_string)) . "</a><br />";
		}
		$out.= "</p>";
	} else {
		$out.= "<p class=\"small_p\">You've not answered any questions, <a href=\"" . get_site_URL() . "\">best fix that</a>.</p>";
	}
	
	return $out;
}
