<?php

// presentation functions

// True or False
function is_question()
{
	global $is_question, $question;
	return $is_question && $question;
}

function is_random_question()
{
	global $is_question, $is_random_question, $question;
	return $is_question && $question && $is_random_question;
}

function is_admin_page()
{
	global $url_array;
	return ($url_array[0] == "admin");
}

function is_competition_page()
{
	global $url_array;
	return ($url_array[0] == "competition");
}

function is_admin()
{
	global $user;
	if ($user)
	{
		if ($user->get_Name() == "Sausage Roller")
		{
			return true;
		}
	}
	return false;
}

// set functions
function set_page_subtitle($req_subtitle)
{
	global $page_subtitle;
	$page_subtitle = $req_subtitle;
}

// get functions
function get_page_subtitle()
{
	global $page_subtitle;
	if ($page_subtitle)
	{
		return $page_subtitle;
	}
	else
	{
		return "Turn left and learn the rules.";
	}
}


function is_remebering_results()
{

	if (count($_SESSION['random_questions_asked']) > 0)
	{
		return true;
		
	}
	else
	{
		return false;
	}
	
}

function get_remebered_string()
{
	global $random_questions_to_remeber;
	// generate a string recapping the remebered data

	$correct_count = 0;
	
	if (count($_SESSION['random_questions_asked']) > 0)
	{
		/*
		$result .= "The site is remembering the last <strong>" . count($_SESSION['random_questions_asked']) . "</strong> question";
		if (count($_SESSION['random_questions_asked'])!=1) 
		{ 
			$result .= "s";
		}
		$result .= " you've answered. ";
		*/
		
		// add the success percentage
		if (count($_SESSION['random_questions_results']) > 0)
		{
			foreach ($_SESSION['random_questions_results'] as $tmp_result)
			{
				if ($tmp_result)
				{
					$correct_count++;
				}
			}
			
			$perc_value = round ((($correct_count / count($_SESSION['random_questions_results'])) * 100), 2);
			$perc_colour = get_colour_from_percentage($perc_value);
			
			$result .= "You have a current success rate of <span style=\"font-weight:bold; color:" . $perc_colour . "\">" . $perc_value . "%</span> (" . $correct_count . " correct out of " . count($_SESSION['random_questions_results']) . ").";
		}
		
		// add the winning streak
		
		// if the most recent response was correct
		if ($_SESSION['random_questions_results'][count($_SESSION['random_questions_results'])-1])
		{
			$current_streak = 1;
			$lost_streak = false;
		}
		else
		{
			$current_streak = 0;
			$lost_streak = true;
		}
		
		for ($i = count($_SESSION['random_questions_results'])-2; $i>=0; $i--)
		{
			if ($_SESSION['random_questions_results'][$i])
			{
				$current_streak ++;
			}
			else
			{
				break;
			}
		}
		
		
		if ($current_streak > 4)
		{
			// currently on a winning streak
			if ($lost_streak == false)
			{
				$result .= " You are on a winning streak of <strong>" . $current_streak . "</strong>. ";
			}
			if ($lost_streak == true)
			{
				$result .= " <span style=\"color:#FF0000\">You just ended your streak of <strong>" . $current_streak . "</strong></span>. ";
			}	
		}
		
		if (count($_SESSION['random_questions_asked']) > ($random_questions_to_remeber * 0.9))
		{
			$result .= " The site only remembers not to ask you the last " . $random_questions_to_remeber . " questions you've answered."; 
		}
		
		// add the forgert link
		$result .= " <a href=\"" . get_site_URL() . "forget\">Forget</a>.";
		
	}
	else
	{
		$result .= "You've not answered any questions recently.";
	}
	
	return $result;
}

function get_site_URL()
{
	global $site_URL;
	return $site_URL;
}

function get_CSS_URL()
{
	return get_site_URL() . "presentation/style.css";
}

function get_theme_directory()
{
	return get_site_URL() . "presentation/";
}

function is_error()
{
	global $error_string;
	if ($error_string)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_error_string($nice_formatting = true)
{
	global $error_string;
	if ($nice_formatting)
	{
		return htmlentities($error_string);
	}
	else
	{
		return $error_string;
	}
}

function add_google_chart_drawChart($req_script)
{
	global $drawChart_script_array;
	$drawChart_script_array[] = $req_script;
}

function get_google_chart_script()
{
	global $drawChart_script_array;
	if ($drawChart_script_array)
	{
		$script_string = implode("\n", $drawChart_script_array);
		
		$script .= '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
	   	$script .= '<script type="text/javascript">
	      google.load("visualization", "1", {packages:["corechart"]});
	      google.setOnLoadCallback(drawChart);
	      function drawChart() {
	        ' . $script_string . '
	      }
	    </script>';
	}
	return $script;
}

function get_recent_wrong_questions()
{
	global $user, $mydb;
	// get all the incorrect responses from the past 2 weeks
	
	$questions = $mydb->get_questions_from_User_ID($user->get_ID(), 20, 1209600, true);
	
	if ($questions)
	{
		$out .= "<h3>Recent questions you got wrong</h3>";
		$out .= "<p class=\"small_p\">";
		foreach ($questions as $question)
		{
			$question_string = $question->get_Text();
			if (strlen($question_string) > 100)
			{
				$question_string = substr($question_string, 0, 97) . "...";
			}
			
			$out .= $question->get_Section() . " <a href=\"" . $question->get_URL() . "\">" . htmlentities(stripslashes($question_string)) . "</a><br />";
		}
		$out .= "</p>";
	}
	
	return $out;
}

function get_recent_questions()
{
	global $user, $mydb;
	// get all the  responses from the past 24 hours
	
	$questions = $mydb->get_questions_from_User_ID($user->get_ID(), 20, 86400, false);
	
	$out .= "<h3>All the questions you've answered in the past 24 hours</h3>";
	
	if ($questions)
	{
		$out .= "<p class=\"small_p\">";
		foreach ($questions as $question)
		{
			$question_string = $question->get_Text();
			if (strlen($question_string) > 100)
			{
				$question_string = substr($question_string, 0, 97) . "...";
			}
			
			$out .= $question->get_Section() . " <a href=\"" . $question->get_URL() . "\">" . htmlentities(stripslashes($question_string)) . "</a><br />";
		}
		$out .= "</p>";
	}
	else 
	{
		$out .= "<p class=\"small_p\">You've not answered any questions, <a href=\"" . get_site_URL() . "\">best fix that</a>.</p>";
	}
	
	return $out;
}

// have one million questions been answered?
function is_one_million()
{
	return true;
	// global $mydb, $competition_value;
	// return ($mydb->get_response_count() >= $competition_value);
}

function time_string_to_million()
{
	global $mydb, $competition_value;
	
	$per_day_rate = $mydb->get_response_count_since(gmmktime() - 86400);
	$per_hour_rate = round($per_day_rate / 24);
	
	$questions_remaining = $competition_value  - $mydb->get_response_count();
	
	
	if ($questions_remaining > 0)
	{
		$hours_remaining = round($questions_remaining / $per_hour_rate);
		
		if ($hours_remaining < 1)
		{
			$out .= "Less than 1 hour";
		}
		else
		{
			if ($hours_remaining > 24)
			{
				// more than 1 day
				$days_remaining = floor($questions_remaining / $per_day_rate);
				
				if ($days_remaining == 1)
				{
					$out .= "1 day, ";
				}
				else
				{
					$out .= $days_remaining . " days, ";
				}
				
				$hours_remaining = $hours_remaining - ($days_remaining*24);
			}
				
			if ($hours_remaining == 1)
			{
				$out .= "1 hour";
			}
			else
			{
				$out .= $hours_remaining . " hours";
			}	
		}	
	}
	else
	{
		$out .= "No time remaining.";
	}
	
	//echo "$questions_remaining, $per_day_rate, $hours_remaining";
	
	return $out;
}

function is_competiton_on()
{
	// end of competition timestamp = 1344406639
	//return false;
	
	if ((1344406639 - gmmktime()) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
	
}
function time_string_to_competition_end()
{
	//$timestamp_of_millionth = 1343197039;
	
	$timestamp_end_of_competition = 1344406639 - gmmktime();
	
	if ($timestamp_end_of_competition > 0)
	{
		$hours_remaining = round($timestamp_end_of_competition / (60*60));
		
		if ($hours_remaining < 1)
		{
			$out .= "Less than 1 hour";
		}
		else
		{
			if ($hours_remaining > 24)
			{
				// more than 1 day
				$days_remaining = floor($timestamp_end_of_competition / (60*60*24));
				
				if ($days_remaining == 1)
				{
					$out .= "1 day, ";
				}
				else
				{
					$out .= $days_remaining . " days, ";
				}
				
				$hours_remaining = $hours_remaining - ($days_remaining*24);
			}
				
			if ($hours_remaining == 1)
			{
				$out .= "1 hour";
			}
			else
			{
				$out .= $hours_remaining . " hours";
			}	
		}	
	}
	else
	{
		$out .= "No time remaining.";
	}
	
	
	return $out;
}

function get_formatted_admin_report($report)
{
	$out .= '<br />
		 <a href="' . get_site_URL() . 'admin/edit/' . $report->get_Question_ID() . '">' . $report->get_Question_ID() . '</a>
		(<a href="' . get_site_URL() . 'admin/?update_report=' . $report->get_ID() . '&new_status=fixed">fixed</a>, 
		 <a href="' . get_site_URL() . 'admin/?update_report=' . $report->get_ID() . '&new_status=incorrect">incorrect</a>, 
		 <a href="' . get_site_URL() . 'admin/?update_report=' . $report->get_ID() . '&new_status=clarified">clarified</a>, 
		 <a href="' . get_site_URL() . 'admin/?update_report=' . $report->get_ID() . '&new_status=noaction">no action taken</a>):' . htmlentities(stripslashes($report->get_Text())) . '<br />';
	
	return $out;
}

function get_competition_footer_string()
{
	global $mydb, $user, $competition_min_questions, $competition_min_perc;
	
	$out .= "
		<p style=\"font-size:14px;\"><strong>Competition!</strong></p>
	";
	
	if (is_competiton_on())
	{
	if (!is_logged_in()) 
		{
		$out .= "
			<p style=\"width: 100%;\">
				In celebration of the millionth question being answered we're having a prize draw. 
				If you want to be entered into the draw to win gift certificates, t-shirts, toe-stops 
				and supplements simply <a href=\"http://rollerderbytestomatic.com/profile\"><strong>log in</strong></a> and answer questions. <a href=\"http://rollerderbytestomatic.com/competition\">Full competition details here</a>.</p>
	
			<p style=\"width: 100%;\">
				If you don't have an account yet they're <a href=\"http://rollerderbytestomatic.com/profile#signup\">easy to set up</a>.
			</p>	
			";
		} 
		else 
		{
			$out .= "
			<p style=\"width: 100%;\">
				In celebration of the millionth question being answered we're having a prize draw to win gift certificates, t-shirts, toe-stops and supplements. 
				<a href=\"http://rollerderbytestomatic.com/competition\">Full competition details here</a>.
			</p>
			";
			
			$timestamp_millionth = 1343197039;
			
			$responses_since_million = $mydb->get_responses_from_User_ID($user->get_ID(), $timestamp_millionth, 1344406639);
			
			$total_count = 0;
			$correct_count = 0;
			
			if ($responses_since_million)
			{
				foreach ($responses_since_million as $response)
				{
					$total_count++;
					if ($response->is_correct())
					{
						$correct_count++;
					}
				}
			}
			
			if ($total_count > 0)
			{
				$perc_value = round ((($correct_count / $total_count) * 100), 2);
			}
			$perc_colour = get_colour_from_percentage($perc_value);
			
			
			// have they answered enough questions
			// have they gotten enough correct
			if ($total_count < $competition_min_questions)
			{
				$out .= "	
				<p style=\"width: 100%;\">
					<span style=\"color:red; font-weight: bold;\">You need to <a href=\"http://rollerderbytestomatic.com\"><strong>answer more questions</strong></a> to be entered into the prize draw.</span> Only questions you've answered since the competition began will count.
				</p>
				";
			}
			elseif ($perc_value < $competition_min_perc)
			{
				$out .= "		
				<p style=\"width: 100%;\">
					<span style=\"color:red; font-weight: bold;\">You need to <a href=\"http://rollerderbytestomatic.com\"><strong>answer more questions correctly</strong></a> to be entered into the prize draw.</span> You need to get at least <span style=\"color:" . get_colour_from_percentage(100) . "><i>at least</i> 80&#37;</span> of the questions correct since the competition began to qualify.
				</p>
				";
			}
			else
			{
				$out .= "			
				<p style=\"width: 100%;\">
					<span style=\"color:green; font-weight: bold;\">You've answered enough questions and got enough correct! Yey!</span>
				</p>
				<p style=\"width: 100%;\">
					What now? Well, I guess you should probably <a href=\"http://rollerderbytestomatic.com\">answer more questions</a>, because it's fun!
				</p>
				";
			}
			
			// what are they currently on
			if ($total_count > 0)
			{
				$out .= "		
				<p style=\"width: 100%;\">
					Since the competition started you've answered <strong>" . $total_count . "</strong> question";
					if ($total_count != 1) 
						$out .= "s";
				$out .= " and have a success rate of <span style=\"font-weight:bold; color:" . $perc_colour . "\">" . $perc_value . "%</span>.
				</p>
				";
			}
		}
		$out .= "
		<p style=\"width: 100%;  text-align: right;\">
			The competition ends in <strong>" . time_string_to_competition_end() . "</strong>.
		</p>
		";
	}
	else
	{
		$out .= "
		<p style=\"width: 100%;\">
			The competition has now closed and the prizes will be drawn shortly. To find out if you've been entered into the prize draw, go to the <a href=\"http://rollerderbytestomatic.com/competition\">competition details page here</a>.
		</p> 
	";
	}
	return $out;
}

function get_page_description()
{
	if (is_random_question())
	{
		$out = "An online, free, Roller Derby rules test with hundreds of questions. Turn left and learn the rules.";
	}
	elseif (is_question()) 
	{ 
		$out = htmlspecialchars(stripslashes($question->get_Text())); 
	} 
	elseif (is_competition_page()) 
	{ 
		$out = "Answer Roller Derby questions to win t-shirts, toe-stops and online gift vouchers!"; 
	}
	else 
	{ 
		$out = "An online, free, Roller Derby rules test with hundreds of questions. Turn left and learn the rules.";
	}
	return $out;
}
?>