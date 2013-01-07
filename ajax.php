<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */

// include needed files
include('include.php');

// start the session
session_start();
try 
{
	// create necessary objects
	set_up_database();
	set_up_logged_in_user();
	//set_up_url_array();
	
	// process and return the ajax request
	switch ($_POST['call']) 
	{
		case "save_response":
			$out = ajax_save_response();	
		break;	
		case "remebered_questions_count":
			$out = ajax_remebered_questions_count();		
		break;	
		case "remebered_questions_percentage":
			$out = ajax_remebered_questions_percentage();	
		break;	
		case "remebered_questions_string":
			$out = ajax_remebered_questions_string();	
		break;	
		case "count_responses":
			$out = ajax_count_responses();	
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
		case "get_competition_string":
			$out = ajax_competition_string();	
		break;
		case "get_competition_list":
			$out = ajax_get_admin_competition_list();	
		break;
		
	}
	
	echo $out;
}
catch (Exception $e) 
{
	save_log("error", $e->getMessage());
}

function ajax_save_response()
{
	global $mydb, $remeber_in_session, $random_questions_to_remeber;
	// saving the response
	
	// clean the input
	$question_ID = $_POST['question_ID'];
	$response_ID = $_POST['response_ID'];
	settype($question_ID, "integer");
	settype($response_ID, "integer");
	
	// get the question from the ID (tests if ID is valid)
	// $question = get_question_from_ID($question_ID);
	
	// is the answer is correct (will throw exception if it's an invalid ID)
	$response_is_correct = is_answer_correct_from_ID($response_ID);
	
	// get the right User ID
	if (is_logged_in())
	{
		global $user;
		$user_ID = $user->get_ID();
	}
	else
	{
		$user_ID = 0;
	}
	
	// make a new response
	$response = new response(-1, $question_ID, $response_ID, gmmktime(), $response_is_correct, $_SERVER['REMOTE_ADDR'], $user_ID);
	
	// save the response
	$mydb->set_response($response);

	// remeber what question was answered
	if ($remeber_in_session)
	{
		// if we know the last 100, forget one
		if (count($_SESSION['random_questions_asked']) >= $random_questions_to_remeber)
		{
			array_shift($_SESSION['random_questions_asked']);
		}
		$_SESSION['random_questions_asked'][] = $question_ID;
		
		// remeber the results
		$_SESSION['random_questions_results'][] = $response_is_correct;
	}
	
	if ($_POST['return_remebered_questions_string'])
	{
		return get_remebered_string();
	}
	else
	{
		if ($response_is_correct)
		{
			return "CORRECT!";
		}
		else
		{
			return "WRONG!";
		}
	}
}

function ajax_remebered_questions_count()
{
	$result = count($_SESSION['random_questions_asked']);
	settype($result, "integer");
	return $result;
}

function ajax_remebered_questions_percentage()
{
	if (count($_SESSION['random_questions_results']) > 0)
	{
		foreach ($_SESSION['random_questions_results'] as $tmp_result)
		{
			if ($tmp_result)
			{
				$correct_count++;
			}
		}
		
		$result = round ($correct_count / count($_SESSION['random_questions_results']));
	}
	else
	{
		$result = -1;
	}
	
	settype($result, "integer");
	return $result;
}

function ajax_remebered_questions_string()
{
	return get_remebered_string();
}

function ajax_count_responses()
{
	global $mydb;
	return $mydb->get_response_count();
}

function ajax_count_daily_responses()
{
	global $mydb;
	
	$daily_response_count = cache_get("daily_response_count");
	if (!$daily_response_count)
	{
		$daily_response_count = $mydb->get_response_count_since(gmmktime() - 86400);
		cache_set("daily_response_count", $daily_response_count, 600);
	}
	return $daily_response_count;
}

function ajax_count_hourly_responses()
{
	global $mydb;
	return $mydb->get_response_count_since(gmmktime() - 3600);
}

function ajax_count_minutly_responses()
{
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

function ajax_count_questions()
{
	return get_question_count();
}

function ajax_count_answers()
{
	global $mydb;
	return $mydb->get_answer_count();
}

function ajax_count_unique_IPs()
{
	global $mydb;
	return $mydb->get_response_distinct_ip_count();
}

function ajax_count_users()
{
	global $mydb;
	return $mydb->get_user_count();
}


function ajax_save_poll_results()
{
	global $mydb;
	
	for($i=1; $i<11; $i++)
	{
		if ($_POST["answer" . $i] == 1)
		{
			$poll_answers[] = $i;
		}
	}
	
	$user_ip =  $_SERVER['REMOTE_ADDR'];
	$user_ip = $mydb->mysql_res($user_ip);
		

	
	if ($poll_answers)
	{
		// has already voted?
		$query = "SELECT COUNT(*) FROM rdtom_pollresponses WHERE IP = '" .  $user_ip . "'";
		$count = $mydb->get_var($query);
		if ($count)
		{
			if ($user_ip == "109.149.198.135")
			{
				echo "ADMIN ";
			}
			else
			{
				return "Our records indicate you've already voted.";
			}
		}
		
		foreach ($poll_answers as $question_ID)
		{
			
			$query = "
			INSERT INTO rdtom_pollresponses (
				Question_ID ,
				Timestamp ,
				IP
				)
				VALUES (
				'" . $question_ID . "',  '" . gmmktime() . "',  '" .  $user_ip . "'
				);";
			
			$mydb->run_query($query);
		}
	}
	
	if (trim($_POST["answer_other"]))
	{
		save_log("other_features", $_POST["answer_other"]);
	}
	
		
	return "Your answers have been saved. Thanks! <a onclick=\"get_results()\">View results</a>.";
}

function ajax_get_poll_results()
{
	global $mydb;
	
	$query = "
			SELECT 
			count(*) AS responses, Question_ID
			FROM rdtom_pollresponses 
			GROUP BY Question_ID";
	
	$results = $mydb->get_results($query);
	
	//offset from other poll
	$offset_extra[10] = 1;
	$offset_extra[1] = 7;
	$offset_extra[2] = 16;
	$offset_extra[3] = 8;
	$offset_extra[4] = 11;
	$offset_extra[5] = 10;
	$offset_extra[6] = 0;
	$offset_extra[7] = 3;
	$offset_extra[8] = 8;
	$offset_extra[9] = 5;
	
	$highest_count = 0;
	$total_votes = 0;
	foreach ($results as $result)
	{
		$poll_count[$result['Question_ID']] = $result['responses'] + $offset_extra[$result['Question_ID']];
		$total_votes += $poll_count[$result['Question_ID']];
		if ($highest_count < $poll_count[$result['Question_ID']])
		{
			$highest_count = $poll_count[$result['Question_ID']];
		}
	}
	
	$highest_count = round($highest_count * 1.3);
	$highest_percentage = round(($highest_count * 100)/ $total_votes);
	
	$poll_questions = array();

	$poll_questions[10] = "Other rule sets (e.g. No Minors Beta, USARS, WORD)";
	$poll_questions[1] = "Questions for officials (refs & NSOs)";
	$poll_questions[2] = "[Done] Stats showing what sections you're good/bad at";
	$poll_questions[3] = "Select which questions you will be given (difficulty, rules sections etc.)";
	$poll_questions[4] = "A free mobile app";
	$poll_questions[5] = "Auto-generated mock tests";
	$poll_questions[6] = "Other languages";
	$poll_questions[7] = "User submitted (and moderated) questions";
	$poll_questions[8] = "Questions with images";
	$poll_questions[9] = "Discussion areas to discuss specific questions, rules, and the site in general";
	
	$extra_letter_index = 65; 
	
	foreach ($poll_count as $question_ID => $question_count)
	{
		
		$percentage = round(($question_count * 100)/ $total_votes);
		
		$extra_letter_index ++;
		$sortable_index = str_pad((int) $percentage,3,"0",STR_PAD_LEFT) . chr($extra_letter_index);
		
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

function ajax_get_admin_questions_list()
{
	global $mydb;
	if ($_POST['search'])
	{
		$questions = get_questions_search($_POST['search']);
	}
	else
	{
		$questions = get_questions();
	}
	
	if ($questions)
	{
		foreach ($questions as $question)
		{
			$section_string = $question->get_Section();
			$section_array = explode("." , $section_string);
			
			
			$div_class_array = array();
			
			// filter by section
			$div_class_array[] = "section_$section_array[0]";
			$div_class_array[] = "section_$section_array[0]_$section_array[1]";
			$div_class_array[] = "section_$section_array[0]_$section_array[1]_$section_array[2]";
			
			// filter by rule set
			$terms = $question->get_terms("rule-set");
			if ($terms)
			{
				foreach ($terms as $term)
				{
					$div_class_array[] = $term->get_Name();
				}
			}
			$div_class_array_string = implode(" ", $div_class_array);
			
			$out .= "<div style=\"clear:left\" class=\" question_string " . $div_class_array_string . "\">" 
				. $question->get_Section() 
				. " <a href=\"" . get_site_URL() . "admin/edit/" . $question->get_ID() . "#edit_question\">" 
				. htmlentities(stripslashes($question->get_Text())) . "</a>
				</div>";
		}
	}
	else
	{
		$out .= "<p>No questions found.</p>";
	}
	
	return $out;
}

function ajax_string_to_one_million()
{
	return time_string_to_million();
}

function ajax_competition_string()
{
	return get_competition_footer_string();
}

function ajax_get_admin_competition_list()
{
	global $mydb;
	// return a list of people entered into the prize draw competition
	
	// get all the responses
	$all_raw_responses = $mydb->get_responses_raw_between(1343197039, 1344406639);
	
	// get all the users
	$all_users = $mydb->get_users();
	
	// get stats from the results
	// $user_data_array[USER_ID][0] = wrong, [1] = correct
	foreach ($all_raw_responses as $raw_response)
	{
		if ($raw_response["Correct"] == 1)
		{
			$user_data_array[$raw_response["User_ID"]][1]++;
		}
		else
		{
			$user_data_array[$raw_response["User_ID"]][0]++;
		}
	}
	
	// find all the users with 50 or more questions answered
	
	foreach ($user_data_array as $user_ID => $user_data)
	{
		if ($all_users[$user_ID])
		{
			if (($user_data[0] + $user_data[1]) >= 50)
			{
				$perc_value = round ((($user_data[1] / ($user_data[0] + $user_data[1])) * 100), 2);
				$perc_colour = get_colour_from_percentage($perc_value);
				if ($perc_value >= 80)
				{
					$out .= "<br />" . htmlentities(stripslashes($all_users[$user_ID]->get_Name())) . " [" . ($user_data[0] + $user_data[1]) . " <span style=\"font-weight:bold; color:" . $perc_colour . "\">" . $perc_value . "%</span>]";
					$entry_counter ++;
				}
				else
				{
					$out_2 .= "<br />" . htmlentities(stripslashes($all_users[$user_ID]->get_Name())) . " [" . ($user_data[0] + $user_data[1]) . " <span style=\"font-weight:bold; color:" . $perc_colour . "\">" . $perc_value . "%</span>]";
					$entry_almost_counter ++;
				}
			}
		}
		else
		{
			$perc_value = round ((($user_data[0] / ($user_data[0] + $user_data[1])) * 100), 2);
			$perc_colour = get_colour_from_percentage($perc_value);
			$out_3 .= "<br /> UNKNOWN [" . ($user_data[0] + $user_data[1]) . " <span style=\"font-weight:bold; color:" . $perc_colour . "\">" . $perc_value . "%</span>]";
		}
	}
	
	return $entry_counter . " qualified users. " . $entry_almost_counter . " with 50+ responses but not 80%<hr>" . $out . "<hr>" . $out_2 . "<hr>" . $out_3;
}
?>