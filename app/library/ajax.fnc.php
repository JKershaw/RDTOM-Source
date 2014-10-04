<?php
function ajax_save_response() {
	global $mydb;
	$fileCache = new FileCache();
	$session = new Session();
	
	// saving the response
	
	// clean the input
	$question_ID = $_POST['question_ID'];
	$response_ID = $_POST['response_ID'];
	settype($question_ID, "integer");
	settype($response_ID, "integer");
	
	// is the answer is correct (will throw exception if it's an invalid ID)
	$response_is_correct = is_answer_correct_from_ID($response_ID);
	
	// get the right User ID
	if (is_logged_in()) {
		global $user;
		$user_ID = $user->get_ID();
		$fileCache->forget("user_responses_" . $user->get_ID());
	} else {
		$user_ID = 0;
	}
	
	// make a new response
	$response = new response(-1, $question_ID, $response_ID, gmmktime() , $response_is_correct, $_SERVER['REMOTE_ADDR'], $user_ID);
	
	// save the response
	$mydb->set_response($response);
	
		
		$random_questions_asked = $session->get('random_questions_asked');
		$random_questions_results = $session->get('random_questions_results');
		
		// if we know the last 100, forget one
		if (count($random_questions_asked) >= NUMBER_OF_RECENTLY_ASKED_QUESTIONS_TO_REMEMBER) {
			array_shift($random_questions_asked);
		}
		$random_questions_asked[] = $question_ID;
		
		// remeber the results
		$random_questions_results[] = $response_is_correct;
		
		// save to the session
		$session->set('random_questions_asked', $random_questions_asked);
		$session->set('random_questions_results', $random_questions_results);

	
	if ($_POST['return_remebered_questions_string']) {
		return get_remebered_string();
	} else {
		if ($response_is_correct) {
			return "CORRECT!";
		} else {
			return "WRONG!";
		}
	}
}

function ajax_save_responses() {
	global $mydb;
	$fileCache = new FileCache();
	
	// saving the responses
	$answers_array = Array();
	
	// get the right User ID
	if (is_logged_in()) {
		global $user;
		$user_ID = $user->get_ID();
		$fileCache->forget("user_responses_" . $user->get_ID());
	} else {
		$user_ID = 0;
	}
	
	if ($_POST['q_array']) {
		foreach ($_POST['q_array'] as $index => $question_ID) {
			
			// make sure the question ID is valid
			$question = get_question_from_ID($question_ID);
			
			// is the answer ID valid
			$response_is_correct = is_answer_correct_from_ID($_POST['a_array'][$index]);
			
			// save the response
			$response = new response(-1, $question_ID, $_POST['a_array'][$index], gmmktime() , $response_is_correct, $_SERVER['REMOTE_ADDR'], $user_ID);
			
			$mydb->set_response($response);
		}
	}
	
	if ($_POST['test_id'] && $_POST['test_id'] > 0) {
		$test = get_test_from_ID($_POST['test_id']);
		
		// increase the taken count
		$test->set_Complete_Count($test->get_Complete_Count() + 1);
		set_test($test);
	}
}

function ajax_remebered_questions_count() {
	$session = new Session();
	$result = count($session->get('random_questions_asked'));
	settype($result, "integer");
	return $result;
}

function ajax_remebered_questions_percentage() {
	$session = new Session();
	$random_questions_results = $session->get('random_questions_results');
	if (count($random_questions_results) > 0) {
		foreach ($random_questions_results as $tmp_result) {
			if ($tmp_result) {
				$correct_count++;
			}
		}
		
		$result = round($correct_count / count($random_questions_results));
	} else {
		$result = - 1;
	}
	
	settype($result, "integer");
	return $result;
}

function ajax_remebered_questions_string() {
	return get_remebered_string();
}

function ajax_count_responses() {
	global $mydb;
	return $mydb->get_response_count();
}

function ajax_count_api() {
	$fileCache = new FileCache();
	$api_calls = $fileCache->get("api_calls");
	if ($api_calls) {
		return count($api_calls);
	} else {
		return 0;
	}
}

function ajax_count_daily_responses() {
	$fileCache = new FileCache();
	$raw_data = $fileCache->get("stats_hourly_posts");
	$raw_data[24] = $fileCache->get("response_count_last_hour");
	return array_sum($raw_data);
}

function ajax_count_hourly_responses() {
	$fileCache = new FileCache();
	return $fileCache->get("response_count_last_hour");
}

function ajax_count_minutly_responses() {
	global $mydb;
	return $mydb->get_response_count_since(gmmktime() - 60);
}

/*
 * TODO Highest minutly rate
 * 
 * SELECT count(ID) as count, MINUTE(FROM_UNIXTIME(`Timestamp`)) as minute, 
HOUR(FROM_UNIXTIME(`Timestamp`)) as hour, DAYOFYEAR(FROM_UNIXTIME(`Timestamp`)) as day 
FROM rdtom_responses WHERE Timestamp > '1357127466'  GROUP BY minute, hour, day ORDER BY count DESC
*/

function ajax_count_questions() {
	return get_question_count();
}

function ajax_count_answers() {
	global $mydb;
	return $mydb->get_answer_count();
}

function ajax_count_unique_IPs() {
	$fileCache = new FileCache();
	return $fileCache->get("response_distinct_ip_count");
}

function ajax_count_users() {
	global $mydb;
	return $mydb->get_user_count();
}

function ajax_save_poll_results() {
	global $mydb;
	
	for ($i = 1; $i < 11; $i++) {
		if ($_POST["answer" . $i] == 1) {
			$poll_answers[] = $i;
		}
	}
	
	$user_ip = $_SERVER['REMOTE_ADDR'];
	$user_ip = $mydb->mysql_res($user_ip);
	
	if ($poll_answers) {
		
		// has already voted?
		$query = "SELECT COUNT(*) FROM rdtom_pollresponses WHERE IP = '" . $user_ip . "'";
		$count = $mydb->get_var($query);
		if ($count) {
			if ($user_ip == "109.149.198.135") {
				echo "ADMIN ";
			} else {
				return "Our records indicate you've already voted.";
			}
		}
		
		foreach ($poll_answers as $question_ID) {
			
			$query = "
			INSERT INTO rdtom_pollresponses (
				Question_ID ,
				Timestamp ,
				IP
				)
				VALUES (
				'" . $question_ID . "',  '" . gmmktime() . "',  '" . $user_ip . "'
				);";
			
			$mydb->run_query($query);
		}
	}
	
	if (trim($_POST["answer_other"])) {
		save_log("other_features", $_POST["answer_other"]);
	}
	
	return "Your answers have been saved. Thanks! <a onclick=\"get_results()\">View results</a>.";
}

function ajax_get_poll_results() {
	global $mydb, $poll_questions;
	
	// make an empty array with all questions
	foreach ($poll_questions as $poll_question_id => $poll_question_text) {
		$poll_count[$poll_question_id] = 0;
	}
	
	$query = "
			SELECT 
			count(*) AS responses, Question_ID
			FROM rdtom_pollresponses 
			GROUP BY Question_ID";
	
	$results = $mydb->get_results($query);
	
	$highest_count = 0;
	$total_votes = 0;
	foreach ($results as $result) {
		$poll_count[$result['Question_ID']] = $result['responses'];
		$total_votes+= $poll_count[$result['Question_ID']];
		if ($highest_count < $poll_count[$result['Question_ID']]) {
			$highest_count = $poll_count[$result['Question_ID']];
		}
	}
	
	$highest_count = round($highest_count * 1.3);
	$highest_percentage = round(($highest_count * 100) / $total_votes);
	
	$extra_letter_index = 65;
	
	foreach ($poll_count as $question_ID => $question_count) {
		
		$percentage = round(($question_count * 100) / $total_votes, 1);
		
		$extra_letter_index++;
		$sortable_index = str_pad((int)$question_count, 3, "0", STR_PAD_LEFT) . chr($extra_letter_index);
		
		$html_array[$sortable_index] = "<span style=\"font-size:14px; float:left;\">" . $poll_questions[$question_ID] . "</span>
		<span title=\"" . $question_count . " votes\" style=\"font-size:14px; float:right;\">" . $percentage . "%</span>
		<br />
		<div style=\"width:100%; height:10px; border: 1px solid lightgrey; margin:1px; clear: both;\">
			<div style=\"width:" . round((($percentage * 100) / $highest_percentage)) . "%; height:10px; background:lightblue;\">
			</div>
		</div>
		<br />
		";
	}
	
	ksort($html_array);
	$html_array = array_reverse($html_array);
	
	return implode(" ", $html_array);
}

function ajax_get_admin_questions_list() {
	global $mydb;
	if ($_POST['search']) {
		$questions = get_questions_search($_POST['search']);
	} else {
		$questions = get_questions();
	}
	
	if ($questions) {
		foreach ($questions as $question) {
			$section_string = $question->get_Section();
			$section_array = explode(".", $section_string);
			
			$div_class_array = array();
			
			// filter by section
			$div_class_array[] = "section_$section_array[0]";
			$div_class_array[] = "section_$section_array[0]_$section_array[1]";
			$div_class_array[] = "section_$section_array[0]_$section_array[1]_$section_array[2]";
			
			// filter by rule set
			$terms = $question->get_terms("rule-set");
			if ($terms) {
				foreach ($terms as $term) {
					$div_class_array[] = $term->get_Name();
				}
			}
			$div_class_array_string = implode(" ", $div_class_array);
			
			$out.= "<div style=\"clear:left\" class=\" question_string " . $div_class_array_string . "\">
			<!-- <a onclick=\"$('#extra_" . $question->get_ID() . "').toggle();\">+</a> --> " . $question->get_Section() . " <a href=\"" . get_site_URL() . "admin/edit/" . $question->get_ID() . "#edit_question\">" . htmlentities(stripslashes($question->get_Text())) . "</a>
				<span class=\"extra_all\" id=\"extra_" . $question->get_ID() . "\" style=\"display:none;\">
				<p style=\"font-size: 10px; margin-left: 1em;\">
				";
			$out.= get_admin_terms_checkboxes_ajax("tag", $question);
			$out.= "
				</p>
				</span>
				</div>";
		}
	} else {
		$out.= "<p>No questions found.</p>";
	}
	
	return $out;
}

function ajax_string_to_one_million() {
	return time_string_to_million();
}

function ajax_get_admin_set_relationship() {
	global $mydb;
	if (!is_admin()) {
		return "";
	}
	
	$mydb->add_relationship($_POST['questionID'], $_POST['termID']);
}

function ajax_save_comment() {
	global $user;
	
	// saving a comment
	
	$question_comment_question_id = $_POST['question_id'];
	$question_comment_text = $_POST['text'];
	
	// get the question from the ID (tests if ID is valid)
	$question = get_question_from_ID($question_comment_question_id);
	
	if (!$question) {
		throw new exception("Comment attempted to be saved for an invalid Question ID");
	}
	
	// get the right User ID
	if (!is_logged_in()) {
		throw new exception("Must be logged in to save a comment");
	}
	
	// make a new comment
	$comment = new comment(-1, $user->get_ID() , $question_comment_question_id, gmmktime() , $question_comment_text, QUESTION_COMMENT);
	
	// save the comment
	set_comment($comment);
	
	echo "Saved!";
}

function ajax_random_forum_thread() {
	$thread = get_thread_from_random();
	if ($thread) {
		return "<a href=\"" . $thread->get_URL() . "\">" . htmlentities(stripslashes($thread->get_Title())) . "</a>";
	}
}

function ajax_latest_forum_thread() {
	$thread = get_latest_thread();
	if ($thread) {
		$latest_post = $thread->get_latest_post();
		return "<a href=\"" . $thread->get_URL() . "\">&quot;" . htmlentities(stripslashes($thread->get_Title())) . "&quot; by " . htmlentities(stripslashes($latest_post->get_author()->get_Name())) . "</a>";
	}
}

function ajax_stats_user_progress() {
	global $responses_needed_for_section_breakdown;
	
	if (is_admin() && $_REQUEST['User_ID']) {
		global $mydb;
		$user_responses = $mydb->get_responses_from_User_ID($_REQUEST['User_ID'], true);
		$user_ID = (int)$_REQUEST['User_ID'];
	} else {
		$user_responses = return_user_responses();
	}
	
	if (!$user_responses || (count($user_responses) < $responses_needed_for_section_breakdown)) {
		return "
{
  \"cols\": [
         {\"id\":\"\",\"label\":\"point\",\"pattern\":\"\",\"type\":\"number\"},
         {\"id\":\"\",\"label\":\"percentage\",\"pattern\":\"\",\"type\":\"number\"}
        ],
  \"rows\": [
        ]
}	";
	}
	
	// Generate data of progress, a 10 point floating average
	$raw_data = Array();
	foreach ($user_responses as $response) {
		if ($response->is_correct()) {
			$raw_data[] = 100;
		} else {
			$raw_data[] = 0;
		}
	}
	
	// get a floating point average and remove the ends, so we lose 20 points
	$averaged_data = get_average_of_array($raw_data, 10);
	$averaged_data = array_slice($averaged_data, 10);
	array_splice($averaged_data, -10);
	
	// do the same again, we lose a further 6 points
	$averaged_data = get_average_of_array($averaged_data, 3);
	$averaged_data = array_slice($averaged_data, 3);
	array_splice($averaged_data, -3);
	
	foreach ($averaged_data as $id => $data_point) {
		$averaged_data_string[] = "\n{\"c\":[{\"v\":" . $id . ",\"f\":null},{\"v\":" . $data_point . ",\"f\":null}]}";
	}
	
	$data_string = implode(", ", $averaged_data_string);
	
	return "
{
  \"cols\": [
         {\"id\":\"\",\"label\":\"point\",\"pattern\":\"\",\"type\":\"number\"},
         {\"id\":\"\",\"label\":\"percentage\",\"pattern\":\"\",\"type\":\"number\"}
        ],
  \"rows\": [" . $data_string . "
        ]
}	";
}

function ajax_stats_user_section_totals() {
	$fileCache = new FileCache();
	$user_responses = return_user_responses();
	$user_questions_sections = return_user_questions_sections();
	
	if ($user_responses && $user_questions_sections) {
		$data_array = process_sections_responses_into_data($user_responses, $user_questions_sections);
	}
	$average_responses = $fileCache->get("last_10000_sections");
	
	if ($data_array) {
		foreach ($data_array as $id => $percentage) {
			
			$data_string_array[] = "\n{\"c\":[{\"v\":\"Section " . $id . "\",\"f\":null},{\"v\":" . $percentage . ",\"f\":null},{\"v\":" . $average_responses[$id] . ",\"f\":null}]}";
		}
		$data_string = implode(", ", $data_string_array);
	}
	
	return "
{
  \"cols\": [
         {\"id\":\"\",\"label\":\"Section\",\"pattern\":\"\",\"type\":\"string\"},
         {\"id\":\"\",\"label\":\"You\",\"pattern\":\"\",\"type\":\"number\"},
         {\"id\":\"\",\"label\":\"Average\",\"pattern\":\"\",\"type\":\"number\"}
        ],
  \"rows\": [" . $data_string . "
        ]
}	";
}

function ajax_save_test() {
	global $user;
	if (is_logged_in()) {
		try {
			
			// save or add the test
			
			if ($_POST['id'] > 0) {
				
				// editing a test
				$test = get_test_from_ID($_POST['id']);
			} else {
				
				// saving a new test
				$test = new test();
			}
			
			// build the test object
			$test->set_ID($_POST['id']);
			$test->set_Title($_POST['title']);
			$test->set_Description($_POST['description']);
			$test->set_Status($_POST['status']);
			$test->set_link_hash($_POST['link_hash']);
			
			// Questions and Answers (array of IDs)
			if ($_POST['questions_and_answers'] && is_array($_POST['questions_and_answers'])) {
				foreach ($_POST['questions_and_answers'] as $qanda_IDs) {
					$answers = Array();
					$question = get_question_from_ID($qanda_IDs[0]);
					
					if ($qanda_IDs[1] && is_array($qanda_IDs[1])) {
						foreach ($qanda_IDs[1] as $answer_ID) {
							$answers[] = get_answer_from_ID($answer_ID);
						}
					}
					
					$test->add_question($question, $answers);
				}
			}
			
			$test->set_Author_ID($user->get_ID());
			
			return set_test($test);
		}
		catch(Exception $e) {
			return $e->getMessage();
		}
	} else {
		return -1;
	}
}

function ajax_save_test_rating() {
	if (is_logged_in()) {
		global $user;
		set_test_rating($_POST['test_ID'], $_POST['rating'], $user->get_ID());
	} else {
		set_test_rating($_POST['test_ID'], $_POST['rating'], -1);
	}
}
