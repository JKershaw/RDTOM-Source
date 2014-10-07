<?php

// include needed files
include ('../app/include.php');

// process and return the ajax request
try {
	
	// create the database
	set_up_database();
	
	// requests which don't require a user
	
	// those ajax requests which don't need session or user info
	switch ($_REQUEST['call']) {
		case "count_responses":
			$out = ajax_count_responses();
			break;

		case "count_api":
			$out = ajax_count_api();
			break;

		case "count_daily_responses":
			$out = ajax_count_daily_responses();
			break;

		case "count_hourly_responses":
			$out = ajax_count_hourly_responses();
			break;

		case "count_minutly_responses":
			$out = ajax_count_minutly_responses();
			break;

		case "count_questions":
			$out = ajax_count_questions();
			break;

		case "count_answers":
			$out = ajax_count_answers();
			break;

		case "count_users":
			$out = ajax_count_users();
			break;

		case "count_unique_IPs":
			$out = ajax_count_unique_IPs();
			break;

		case "random_forum_thread":
			$out = ajax_random_forum_thread();
			break;

		case "latest_forum_thread":
			$out = ajax_latest_forum_thread();
			break;
	}
	
	// did the switch activate?
	if ($out) {
		echo $out;
		exit;
	}
	
	// these functions reqire a logged in user
	set_up_user();
	
	// process and return the ajax request
	switch ($_REQUEST['call']) {
		case "save_response":
			$out = ajax_save_response();
			break;

		case "save_responses":
			$out = ajax_save_responses();
			break;

		case "save_comment":
			$out = ajax_save_comment();
			break;

		case "remembered_questions_count":
			$out = ajax_remembered_questions_count();
			break;

		case "remembered_questions_percentage":
			$out = ajax_remembered_questions_percentage();
			break;

		case "remembered_questions_string":
			$out = ajax_remembered_questions_string();
			break;

		case "save_poll_results":
			$out = ajax_save_poll_results();
			break;

		case "get_poll_results":
			$out = ajax_get_poll_results();
			break;

		case "string_to_one_million":
			$out = ajax_string_to_one_million();
			break;

		case "get_admin_questions_list":
			$out = ajax_get_admin_questions_list();
			break;

		case "set_admin_relationship":
			$out = ajax_get_admin_set_relationship();
			break;

		case "stats_user_progress":
			$out = ajax_stats_user_progress();
			break;

		case "stats_user_section_totals":
			$out = ajax_stats_user_section_totals();
			break;

		case "save_test":
			$out = ajax_save_test();
			break;

		case "save_test_rating":
			$out = ajax_save_test_rating();
			break;
	}
	
	echo $out;
}
catch(Exception $e) {
	save_log("error_ajax", htmlentities(print_r($_POST, true)) . " " . $e->getMessage());
}