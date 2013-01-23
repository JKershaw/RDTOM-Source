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
					"function" => "last_10000_sections",
					"seconds" => 3600),
				Array (
					"function" => "delete_old_cache_files",
					"seconds" => 7200),
				Array (
					"function" => "delete_old_usertokens",
					"seconds" => 86400)
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

function last_10000_sections()
{
	global $mydb;
	
	$section_array = get_sections_array();
	$recent_responses = $mydb->get_responses(10000);
	
	$data_array = process_sections_responses_into_data($recent_responses, $section_array);
		
	cache_set("last_10000_sections", $data_array);
}

function delete_old_usertokens()
{
	global $mydb;
	// delete tokens older than 90 days
	$mydb->remove_old_token(gmmktime() - 7776000);
}

function delete_old_cache_files()
{
	
	// create a handler for the directory
	$handler = @opendir("filecache");

	if ($handler)
	{
		// open directory and walk through the filenames
		while ($file = readdir($handler)) 
		{
			// if file isn't this directory or its parent, add it to the results
			if ($file != "." && $file != "..") 
			{
				// get the cache, if the cache is out of date it'll be deleted
				cache_get($file);
			}
		}

		 // tidy up: close the handler
		closedir($handler);		
	}
}
?>