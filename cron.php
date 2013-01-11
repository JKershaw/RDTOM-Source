<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */

/*
 * This file is loaded every 5 minutes and must remeber which functions it has fired and when
 */

/*
 * An array holding the Cron jobs
 */

$cron_tasks = Array ( 
				Array (
					"function" => "stats_hourly_posts",
					"seconds" => 300),
				Array (
					"function" => "response_count_last_hour",
					"seconds" => 600),
				Array (
					"function" => "sections_array",
					"seconds" => 3600),
				Array (
					"function" => "last_10000_responses",
					"seconds" => 3600)
			);


// include needed files
include('include.php');

// process and return the cron request
try 
{
	// create the database
	set_up_database();
	
	// load the file containing info on all the cron jobs
	@$cron_tasks_data = unserialize(file_get_contents('cron_tasks_data'));

	if ($cron_tasks_data)
	{
		echo "cron_tasks_data loaded <br />";
	}
	else
	{
		echo "cron_tasks_data not found <br />";
	}
	
	// for each cron job, check if it needs to be fired.
	foreach ($cron_tasks as $cron_task)
	{
		// check with the $cron_tasks_data to see when this function was last fired
		if (($cron_tasks_data[$cron_task['function']] < (gmmktime() -  $cron_task['seconds'])) || ($_GET['force'] == $cron_task['function']))
		{
			
			$cron_task['function']();
			$cron_tasks_data[$cron_task['function']] = gmmktime();
			
			// echo what the job was
			echo "Cron completed: " . $cron_task['function'] . "<br />";
			
			// only execute one cron job per cycle to keep server load light
			break;
		}
	}
	
	// save the data
	file_put_contents('cron_tasks_data', serialize($cron_tasks_data));
}
catch (Exception $e) 
{
	save_log("error_cron", $e->getMessage());
}

echo "Cron checked!";

function stats_hourly_posts()
{
	global $mydb;
	cache_set("stats_hourly_posts", $mydb->get_stats_hourly_posts(24));
}

function response_count_last_hour()
{
	global $mydb;
	$current_minute = date('i');
	$percentage_hour_complete = $current_minute / 60;
	cache_set("response_count_last_hour", $mydb->get_response_count_since(gmmktime() - round($percentage_hour_complete * 3600)));
}

function sections_array()
{
	cache_set("sections_array", get_sections_array());
}

function last_10000_responses()
{
	global $mydb;
	cache_set("last_10000_responses", $mydb->get_responses(10000));
}

?>