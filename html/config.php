<?php
/*
 * Parse the ini file
 */

try {
	$ini_array = parse_ini_file("config.ini");
} catch (Exception $e) {
	echo "There is no config.ini file.";
}

/*
 * Database connection details, stored in the ini files for security
 */

$database_username = $ini_array["database_username"];
$database_userpassword = $ini_array["database_userpassword"];
$database_name = $ini_array["database_name"];
$database_host = $ini_array["database_host"];

$database_salt = $ini_array["database_salt"];

/*
 * Email details
 */

$smtp_username = $ini_array["smtp_username"];
$smtp_userpassword = $ini_array["smtp_userpassword"];
$smtp_host = $ini_array["smtp_host"];

/*
 * Other values which don't need to be kept as secure
 */

// when getting random questions, how many does the site remeber to avoid dupes?
$random_questions_to_remeber = 100;

// how many times will the site use fancy code to try to find a unique new question before resourting to slow code?
$random_question_find_attempts = 10;

// do we remeber using the session?
$remeber_in_session = true;

// what date format do log files have with their name
$log_file_date_format = "ym_F";

// How many questions does a user need to answer before a section by section breakdown is shown?
$responses_needed_for_section_breakdown = 50;

// When requesting a password reset, how many seconds is the token valid for?
$password_reset_token_expire = 86400;

// the competition value
$competition_value = 1000000;
// minimum questions needed to answer to be eligable
$competition_min_questions = 50;
// minimum correct percentage
$competition_min_perc = 80;


// the current site URL (different on development & test servers so goes in the config file
$site_URL = $ini_array["site_URL"];

// when emailing, this is the from account
$email_from_address = "auto@rollerderbytestomatic.com";
$email_from_name = "Roller Derby Test O'Matic";

/*
 * When fetching questions, this is the deafult search parameter
 */

$default_terms_array = array("rule-set" => "WFTDA6");

/*
 * DEFINITIONS - THESE NEVER CHANGE
 */

// Report status
define("REPORT_OPEN", 0);
define("REPORT_INCORRECT", 1);
define("REPORT_FIXED", 2);
define("REPORT_CLARIFIED", 3);
define("REPORT_NOACTION", 4);

// number of answers listed on the admin page
define("NUMBER_OF_ANSWERS" , 10);


/*
 * Questions in the online poll
 */


$poll_questions[1] = "Questions for officials (refs & NSOs)";
$poll_questions[2] = "More detailed stats showing what sections you're good/bad at";
$poll_questions[3] = "Filtering on what questions you're being given (difficulty, topic etc.)";
$poll_questions[4] = "An Android app";
$poll_questions[5] = "An iPhone/iPad app";
$poll_questions[6] = "Other languages";
$poll_questions[7] = "User submitted (and moderated) questions";
$poll_questions[8] = "Questions with images (ref hand signals, diagrams etc.)";
$poll_questions[9] = "Discussion areas to discuss specific questions, rules, and the site in general";
$poll_questions[10] = "Other rule sets (e.g. USARS, WORD)";
$poll_questions[11] = "A support or donations page";
$poll_questions[12] = "Public high scores";
$poll_questions[12] = "An updated page layout and design";
?>