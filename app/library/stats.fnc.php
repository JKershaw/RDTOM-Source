<?php
function return_stats_user_totals()
{
	global $user, $mydb;
	
	$user_responses = return_user_responses();
	
	if (!$user_responses)
	{
		return "<p>You have not answered any questions whilst logged in. When you do, your stats will appear here.</p>";
	}
	
	// total questions answered
	$total_response_count = count($user_responses);
	
	$total_response_count_string = number_format($total_response_count);
	$out .= "<p>You have answered a total of <strong>" . $total_response_count_string . "</strong> question";
	if ($total_response_count != 1)
	{
		$out .= "s";
	}
	$out .= ".</p>";		
	
	
	// total correct %
	foreach ($user_responses as $user_response)
	{
		if ($user_response->get_Correct())
		{
			$all_time_correct_count++;
		}
		else 
		{
			$all_time_incorrect_count++;
		}
	}
	
	if ($total_response_count > 0)
	{
		$perc_value = round(($all_time_correct_count * 100) / $total_response_count);
	}
	else
	{
		$perc_value = 0;
	}
	
	$perc_colour = get_colour_from_percentage($perc_value);
	$out .= "<p>You have a current total success rate of <span style=\"font-weight:bold; color:" . $perc_colour . "\">" . $perc_value . "%</span> (" . number_format($all_time_correct_count) . " correct out of " . $total_response_count_string . ").</p>";
	
	// recent questions answered
	
	// recent correct %
	return $out;

}

function process_sections_responses_into_data($recent_responses, $section_array)
{
	// create the data to chart
	// for each of the responses

	foreach($recent_responses as $response)
	{
		$section_number = intval($section_array[$response->get_Question_ID()][0]);
		if ($section_number)
		{
			if (($section_number == 1) && ($section_array[$response->get_Question_ID()][1] == "0"))
			{
				$section_number = 10;
			}
			// get the first two values
			if ($response->get_Correct())
			{
				$section_counts[$section_number]["correct"]++;
			}
			else
			{
				$section_counts[$section_number]["wrong"]++;
			}
		}
	}
	
	ksort($section_counts);
	
	foreach ($section_counts as $id => $section_count)
	{
		$percentage = round(($section_count['correct'] * 100) / ($section_count['correct'] + $section_count['wrong']));
		$data_array[$id] = $percentage;
	}

	return $data_array;
}

function return_chart_section_percentages_all() 
{
	
	$data_array = cache_get("last_10000_sections");
	
	if ($data_array)
	{
		return return_chart_section_percentages($data_array);
	}
	else
	{
		return "Cache not found";
	}
	
}

function return_stats_user_section_totals()
{
	global $user;
	
	if (!return_user_responses())
	{
		return;
	}
	
	if ($user)
	{
		$user_call_string = ", User_ID: " . $user->get_ID();
	}
	
	$drawChart_string = '

	var jsonData_user_section_totals = $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {call: "stats_user_section_totals" ' . $user_call_string . '},
            dataType:"json",
            async: false
            }).responseText;
    
	if (JSON.parse(jsonData_user_section_totals).rows.length > 0)
	{
		data_user_section_totals = new google.visualization.DataTable(jsonData_user_section_totals);
	    
        options_user_section_totals = {
          colors: [\'#0000FF\', \'#BBBBFF\'],
          focusTarget: \'category\',
          titlePosition: \'none\',
          hAxis: {titlePosition: \'none\',},
          chartArea: {left:30, width: \'80%\', height: \'80%\', top: 10},
          legend: {position: \'' . $legends . '\'},
          vAxis: {minValue: 0, maxValue: 100, gridlines: {count: 11}}
        };
	
	    var chart_user_section_totals = new google.visualization.ColumnChart(document.getElementById(\'chart_section_breakdown\'));
	    chart_user_section_totals.draw(data_user_section_totals, options_user_section_totals);	
	}
	else
	{
		$("#chart_section_breakdown").html("You need to answer more questions to generate this graph.");
	}
    ';

	add_google_chart_drawChart($drawChart_string);
   	
	$out .= '<p>Section breakdown:</p><div id="chart_section_breakdown" style="width: 100%; height: 400px;">Loading ...</div>';
	
	return $out;
	
}


function return_chart_section_percentages($data_array, $data_array2 = false)
{
	
	foreach ($data_array as $id => $percentage)
	{
		if ($data_array2)
		{
			$data_string_array[] = "['Section " . $id . "',  " . $percentage . ",  " . $data_array2[$id] . "]";
		}
		else
		{
			$data_string_array[] = "['Section " . $id . "',  " . $percentage . "]";
		}
	}
	
	$data_string = implode(", ", $data_string_array);
	
	if ($data_array2)
	{
		$data_string_text = "['Section', 'You', 'Average'],";
		$legend = "right";
	}
	else
	{
		$data_string_text = "['Section', 'Percentage Correct'],";
		$legend = "none";
	}
	
	$drawChart_string = '
		var data = google.visualization.arrayToDataTable([
          ' . $data_string_text . '
          ' . $data_string . '
        ]);

        var options = {
          colors: [\'#0000FF\', \'#BBBBFF\'],
          focusTarget: \'category\',
          titlePosition: \'none\',
          hAxis: {titlePosition: \'none\',},
          chartArea: {left:30, width: \'80%\', height: \'80%\', top: 10},
          legend: {position: \'' . $legends . '\'},
          vAxis: {minValue: 0, maxValue: 100, gridlines: {count: 11}}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById(\'chart_section_breakdown\'));
        chart.draw(data, options);';
	
   	add_google_chart_drawChart($drawChart_string);
   	
	
	$out .= '<p>Section breakdown:</p><div id="chart_section_breakdown" style="width: 100%; height: 400px;">Loading ...</div>';
	
	return $out;
}


function return_stats_user_progress($user = false)
{
	$user_responses = return_user_responses();
	if (!$user_responses)
	{
		return;
	}
	
	if ($user)
	{
		$user_call_string = ", User_ID: " . $user->get_ID();
	}
	
	$drawChart_string = '

	
	var jsonData_user_progress = $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {call: "stats_user_progress" ' . $user_call_string . '},
            dataType:"json",
            async: false
            }).responseText;
    
	if (JSON.parse(jsonData_user_progress).rows.length > 0)
	{
		data_stats_user_progress = new google.visualization.DataTable(jsonData_user_progress);
		
	    options_stats_user_progress = {
	    	  vAxis: {viewWindow:{max:100, min:0},gridlines: {count: 6}, viewWindowMode: \'explicit\'},
	          titlePosition: \'none\',
	          hAxis: {titlePosition: \'none\', textPosition: \'none\'},
          	  chartArea: {left:30, width: \'80%\', height: \'90%\', top: 10},
	          legend: {position: \'none\'},
	          colors: [\'#0000FF\'],
	          curveType: \'function\',
	          enableInteractivity: false
	    };
	
	    var chart_stats_user_progress = new google.visualization.LineChart(document.getElementById(\'chart_progress\'));
	    chart_stats_user_progress.draw(data_stats_user_progress, options_stats_user_progress);	
	}
	else
	{
		$("#chart_progress").html("You need to answer more questions to generate this graph.");
	}
    ';

	add_google_chart_drawChart($drawChart_string);
   	
	$out .= '<p>Your average success rate:</p><div id="chart_progress' . $user_ID . '" style="width: 100%; height: 200px;">Loading ...</div>';
	
	
	/*
	 * Old version - bar charts
	// split data into groups of weeks [YEAR.WEEKNUMBER]
	
	foreach ($user_responses as $response)
	{
		if ($response->is_correct())
		{
			$data[date("oW",$response->get_Timestamp())]["correct"]++;
		}
		else
		{
			$data[date("oW",$response->get_Timestamp())]["wrong"]++;
		}
		
		if (!$lowest_week_value || ($response->get_Timestamp() < $lowest_week_value))
		{
			$lowest_week_value = $response->get_Timestamp();
		}
	}
	
	if (count($data) < 3)
	{
		return "<!-- not enough weeks -->";
	}
	
	// get the total & percentages
	foreach ($data as $week_key => $data_point)
	{
		$data[$week_key]["total"] = $data[$week_key]["correct"] + $data[$week_key]["wrong"];
	}
	

	// fill in the blank weeks between the lowest week count, and now
	$current_week_value = gmmktime();
	for($i = $lowest_week_value; $i <= $current_week_value; $i=$i+604800)
	{
		$week_number = date("oW",$i);
		if (!$data[$week_number])
		{
			$data[$week_number]["total"] = 0;
			//$data[$week_number]["perc"] = 0;
			$data[$week_number]["correct"] = 0;
			$data[$week_number]["wrong"] = 0;
		}
		
		settype($data[$week_number]["total"], "integer");
		//settype($data[$week_number]["perc"], "integer");
		settype($data[$week_number]["correct"], "integer");
		settype($data[$week_number]["wrong"], "integer");
		
	}
	
	// sort the data
	ksort($data);
	
	$current_week_value = date("oW");
	foreach ($data as $week_key => $data_point)
	{
		if ($week_key == $current_week_value)
		{
			$week_string = "This week";
		}
		if ($week_key == ($current_week_value-1))
		{
			$week_string = "Last week";
		}
		if ($week_key < ($current_week_value-1))
		{
			//$week_string = ($current_week_value - $week_key) . " weeks ago";
		}
		
		$data_array[] = "
		['" . $week_string . "', " . $data[$week_key]["wrong"] . ", " . $data[$week_key]["correct"] . "]";
	}
	
	$data_string = implode(", ", $data_array);
	

	// create the chart
	$drawChart_string = '
	var data' . $user_ID . ' = google.visualization.arrayToDataTable([
          [\'Week\', \'Wrong\', \'Correct\'],
          ' . $data_string . '
        ]);

        var options' . $user_ID . ' = {
          titlePosition: \'none\',
          isStacked: true,
          hAxis: {textPosition: "none"},
          colors: [\'' . get_colour_from_percentage(0) . '\', \'' . get_colour_from_percentage(100) . '\', \'#0000FF\', \'#0000FF\'],
          seriesType: "bars",
          focusTarget: "category",
          series: {2: {type: "line"}},
          chartArea:{left:30,top:10,width:"80%", height:"90%"},
        };

        var chart' . $user_ID . ' = new google.visualization.ComboChart(document.getElementById(\'chart_progress' . $user_ID . '\'));
        chart' . $user_ID . '.draw(data' . $user_ID . ', options' . $user_ID . ');';

	add_google_chart_drawChart($drawChart_string);
   	
	$out .= '<p>Weekly progress:</p><div id="chart_progress' . $user_ID . '" style="width: 100%; height: 300px;"></div>';
	
	*/
	
	return $out;
}

function return_chart_24hour_responses()
{
	global $mydb;

	// get the raw values
	$raw_data = cache_get("stats_hourly_posts");
	if (!$raw_data)
	{
		return "No stats_hourly_posts cache found";
	}
	
	$current_minute = date('i');
	$percentage_hour_complete = $current_minute / 60;
	
	// make the final data point the current per hour rate
	$raw_data[24] = cache_get("response_count_last_hour");
	if (!$raw_data[24])
	{
		return "No response_count_last_hour cache found";
	}
	
	
	// use a mix of the current hour plus previous hour to get value
	/*
	if ($current_minute > 0)
	{
		 $raw_data[24] = round($raw_data[24] + ($raw_data[23] * (1 - $percentage_hour_complete)));
	}
	else
	{
		$raw_data[24] = $raw_data[23];
	}
	*/
	
	/*
	 * With an average
	 */
	/*
		// get the floating average
		$tmp_average_data = get_average_of_array($raw_data, 2);
		
		// make the average_data index start at the same place as the raw_data and add 0 at the start
		reset($raw_data);
		$raw_data_first_key = key($raw_data);
		
		foreach ($tmp_average_data as $average_index => $data)
		{
			if (!$data)
			{
				$data = 0;
			}
			$average_data[$raw_data_first_key + $average_index] = $data;
		}
		
		// merge it all into one array
		foreach($raw_data as $id => $response_count)
		{
			$hour_count = 24-$id;
			if ($hour_count > 1)
			{
				$hour_string = $hour_count . " hours ago";
			}
			elseif ($hour_count == 1)
			{
				$hour_string = $hour_count . " hour ago";
			}
			elseif ($hour_count == 0)
			{
				$hour_string = "This hour";
			}
			$data_string_array[] = "
			['". $hour_string . "',  " .(integer)$raw_data[$id] . ",      " .(integer)$average_data[$id] . "]";
		}
		
		$data_string = implode(", ", $data_string_array);
		
		$drawChart_string = '
		var data = google.visualization.arrayToDataTable([
	          [\'Hour\', \'Responses\', \'Average\'],
	          ' . $data_string . '
	        ]);
	
	    var options = {
	          titlePosition: \'none\',
	          hAxis: {titlePosition: \'none\', textPosition: \'none\'},
	          chartArea: {width: \'90%\', height: \'90%\', top: 10},
	          legend: {position: \'none\'},
	          colors: [\'#b1b1e8\', \'#0000FF\']
	    };
	    ';
	*/
	// without average
	
	// merge it all into one array
		foreach($raw_data as $id => $response_count)
		{
			$hour_count = 24-$id;
			if ($hour_count > 1)
			{
				$hour_string = $hour_count . " hours ago";
			}
			elseif ($hour_count == 1)
			{
				$hour_string = $hour_count . " hour ago";
			}
			elseif ($hour_count == 0)
			{
				$hour_string = "This hour";
			}
			$data_string_array[] = "
			['". $hour_string . "',  " .(integer)$raw_data[$id] . "]";
		}
		
		$data_string = implode(", ", $data_string_array);
		
		$drawChart_string = '
		var data = google.visualization.arrayToDataTable([
	          [\'Hour\', \'Responses\'],
	          ' . $data_string . '
	        ]);
	
	    var options = {
	          titlePosition: \'none\',
	          hAxis: {titlePosition: \'none\', textPosition: \'none\'},
	          chartArea: {width: \'90%\', height: \'90%\', top: 10},
	          legend: {position: \'none\'},
	          colors: [\'#0000FF\']
	    };
	    ';
	
	$drawChart_string .= '

        var chart = new google.visualization.LineChart(document.getElementById(\'chart_24responses\'));
        chart.draw(data, options);';

	add_google_chart_drawChart($drawChart_string);
   	
	$out .= '<div id="chart_24responses" style="width: 100%; height: 200px;"></div>';
	
	return $out;
}
function return_user_responses()
{
	global $user, $mydb;
	global $user_responses, $fetched_user_responses;
	
	if ($fetched_user_responses)
	{
		return $user_responses;
	}
	else
	{
		$cached_user_responses = cache_get("user_responses_" . $user->get_ID());
		if(!$cached_user_responses)
		{
			$user_responses = $mydb->get_responses_from_User_ID($user->get_ID(), true);
			$fetched_user_responses = true;
			$cached_user_responses = $user_responses;
			cache_set("user_responses_" . $user->get_ID(), $user_responses, 600);
		}
		
		return $cached_user_responses;
	}
	
	return $user_responses;
}

function return_user_questions_sections($user_ID = false)
{
	global $user, $mydb;
	global $user_questions_sections, $fetched_user_questions_sections;
	
	if ($fetched_user_questions_sections)
	{
		return $user_questions_sections;
	}
	else
	{
		$user_questions_sections = get_sections_array_from_User_ID($user->get_ID());
		$fetched_user_questions_sections = true;
	}
	
	return $user_questions_sections;
}

function set_up_stats_header()
{
	
}