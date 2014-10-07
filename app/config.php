<?php
ini_set('display_errors', 'Off');
error_reporting(E_ERROR);

$ini_array = parse_ini_file("config.ini");

setUpSiteURL($ini_array);
setUpSMTP($ini_array);
setUpDatabase($ini_array);
setUpDefaultTermsArray();
setUpDefinitions();

function setUpSiteURL($ini_array) {
	global $site_URL;

	$site_URL = $ini_array["site_URL"];
}

function setUpSMTP($ini_array) {
	global $smtp_username, $smtp_userpassword, $smtp_host, $smtp_from_name, $smtp_from_address;

	$smtp_username = $ini_array["smtp_username"];
	$smtp_userpassword = $ini_array["smtp_userpassword"];
	$smtp_host = $ini_array["smtp_host"];
	$smtp_from_name = "Roller Derby Test O'Matic";
	$smtp_from_address = "auto@rollerderbytestomatic.com";
}

function setUpDatabase($ini_array) {
	global $database_username, $database_userpassword, $database_name, $database_host, $database_salt;
	
	$database_username = $ini_array["database_username"];
	$database_userpassword = $ini_array["database_userpassword"];
	$database_name = $ini_array["database_name"];
	$database_host = $ini_array["database_host"];
	
	$database_salt = $ini_array["database_salt"];
	
	if (!$database_username) {
		
		//default params
		
		$database_username = "ubuntu";
		$database_userpassword = "";
		$database_name = "circle_test";
		$database_host = "127.0.0.1";
		
		$site_URL = "http://rdtom:8080/";
	}
}

function setUpDefaultTermsArray() {	
	$GLOBALS['default_terms_array'] = array("rule-set" => "WFTDA7");
}

function setUpDefinitions() {

	// Magic numbers of note
	define("NUMBER_OF_RECENTLY_ASKED_QUESTIONS_TO_REMEMBER", 100);
	define("FANCY_CODE_MAXIMUM_ATTEMPT_COUNT", 10);
	define("RESPONSES_NEEDED_FOR_SECTION_BREAKDOWN", 50);
	define("PASSWORD_RESET_TOKEN_TTL", 86400);
	
	// Report status
	define("REPORT_OPEN", 0);
	define("REPORT_INCORRECT", 1);
	define("REPORT_FIXED", 2);
	define("REPORT_CLARIFIED", 3);
	define("REPORT_NOACTION", 4);
	
	// Question comment types
	define("QUESTION_COMMENT", 0);
	define("QUESTION_CHANGED", 1);
	define("QUESTION_DELETED", 2);
	
	// number of answers listed on the admin page
	define("NUMBER_OF_ANSWERS", 10);
}
?>