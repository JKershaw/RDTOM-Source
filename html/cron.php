<?php
/*
 * This file is loaded every 5 minutes and must remeber which functions it has fired and when
*/

// include needed files
include ('../app/include.php');
include_once ("FileCache");

$cron_tasks = Array(
	Array(
		"function" => "stats_hourly_posts",
		"seconds" => 1200
	) ,
	Array(
		"function" => "response_count_last_hour",
		"seconds" => 300
	) ,
	Array(
		"function" => "last_10000_sections",
		"seconds" => 3600
	) ,
	Array(
		"function" => "rebuild_sitemap",
		"seconds" => 86400
	) ,
	Array(
		"function" => "stats_count_unique_IPs",
		"seconds" => 3600
	) ,
	Array(
		"function" => "delete_old_usertokens",
		"seconds" => 86400
	) ,
	Array(
		"function" => "archive_responses",
		"seconds" => 3600
	) ,
	Array(
		"function" => "unarchive_responses",
		"seconds" => 6000
	)
);

// process and return the cron request
try {
	
	// create the database
	set_up_database();
	
	// load the file containing info on all the cron jobs
	@$cron_tasks_data = unserialize(file_get_contents('../filecache/cron_tasks_data'));
	
	if ($cron_tasks_data) {
		echo "cron_tasks_data loaded <br />";
	} else {
		echo "cron_tasks_data not found <br />";
	}
	
	$count = 0;
	
	// for each cron job, check if it needs to be fired.
	foreach ($cron_tasks as $cron_task) {
		
		// check with the $cron_tasks_data to see when this function was last fired
		if (($cron_tasks_data[$cron_task['function']] < (gmmktime() - $cron_task['seconds'])) || ($_GET['force'])) {
			if ($_GET['force'] && ($_GET['force'] != $cron_task['function'])) {
				
				//echo "Force: " . $_GET['force'] . "!=" . $cron_task['function'] . "<br />";
				continue;
			}
			echo "Cron started: " . $cron_task['function'] . "<br />";
			
			$count++;
			
			$cron_task['function']();
			$cron_tasks_data[$cron_task['function']] = gmmktime();
			
			// echo what the job was
			echo "Cron completed: " . $cron_task['function'] . "<br />";
			
			// only execute one cron job per cycle to keep server load light
			if ($count > 1) {
				break;
			}
		}
	}
	
	// save the data
	file_put_contents('../filecache/cron_tasks_data', serialize($cron_tasks_data));
	
	echo "cron_tasks_data saved <br />";
}
catch(Exception $e) {
	save_log("error_cron", $e->getMessage());
}

echo "Cron checked!";