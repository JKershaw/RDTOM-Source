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
					"seconds" => 1200),
				Array (
					"function" => "response_count_last_hour",
					"seconds" => 300),
				Array (
					"function" => "last_10000_sections",
					"seconds" => 3600),
				Array (
					"function" => "rebuild_sitemap",
					"seconds" => 86400),
				Array (
					"function" => "stats_count_unique_IPs",
					"seconds" => 3600),
				Array (
					"function" => "delete_old_usertokens",
					"seconds" => 86400),
				Array (
					"function" => "archive_responses",
					"seconds" => 3600),
				Array (
					"function" => "unarchive_responses",
					"seconds" => 6000)
			);


// include needed files
include('../app/include.php');

// process and return the cron request
try 
{
	// create the database
	set_up_database();
	
	// load the file containing info on all the cron jobs
	@$cron_tasks_data = unserialize(file_get_contents('../filecache/cron_tasks_data'));

	if ($cron_tasks_data)
	{
		echo "cron_tasks_data loaded <br />";
	}
	else
	{
		echo "cron_tasks_data not found <br />";
	}
	
	$count = 0;
	// for each cron job, check if it needs to be fired.
	foreach ($cron_tasks as $cron_task)
	{
		// check with the $cron_tasks_data to see when this function was last fired
		if (($cron_tasks_data[$cron_task['function']] < (gmmktime() -  $cron_task['seconds'])) || ($_GET['force']))
		{
			if ($_GET['force'] && ($_GET['force'] != $cron_task['function']))
			{
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
			if ($count > 1)
			{
				break;
			}
		}
	}
	
	// save the data
	file_put_contents('../filecache/cron_tasks_data', serialize($cron_tasks_data));
	
	echo "cron_tasks_data saved <br />";
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
	cache_set("response_count_last_hour", $mydb->get_response_count_since(gmmktime() - 3600));
}

function last_10000_sections()
{
	global $mydb;
	
	$section_array = get_sections_array();
	$recent_responses = $mydb->get_responses(10000);
	
	// might not get these due to an sql error
	if ($section_array && $recent_responses)
	{
		$data_array = process_sections_responses_into_data($recent_responses, $section_array);
		cache_set("last_10000_sections", $data_array);
	}
}

function delete_old_usertokens()
{
	global $mydb;
	// delete tokens older than 90 days
	$mydb->remove_old_token(gmmktime() - 7776000);
}

function rebuild_sitemap()
{
	global $default_terms_array;
	
	
	// get all the pages into an array
	$sitemap = array (
		1 => array(
			"loc" => "http://rollerderbytestomatic.com",
			//"lastmod" => "2005-01-01",
			"changefreq" => "weekly",
			"priority" => "1"
		),
		
		2 => array(
			"loc" => "http://rollerderbytestomatic.com/about",
			//"lastmod" => "2005-01-01",
			"changefreq" => "weekly",
			"priority" => "1"
		),
		
		3 => array(
			"loc" => "http://rollerderbytestomatic.com/stats",
			//"lastmod" => "2005-01-01",
			"changefreq" => "weekly",
			"priority" => "1"
		),
		
		4 => array(
			"loc" => "http://rollerderbytestomatic.com/stats",
			//"lastmod" => "2005-01-01",
			"changefreq" => "weekly",
			"priority" => "1"
		),
		
		5 => array(
			"loc" => "http://rollerderbytestomatic.com/test",
			//"lastmod" => "2005-01-01",
			"changefreq" => "weekly",
			"priority" => "1"
		)
	);
	
	$all_questions = get_questions($default_terms_array);
	
	foreach ($all_questions as $question)
	{
		$sitemap[] = array(
			"loc" => $question->get_URL(),
			"priority" => "0.5"
		);
	}
	
	// generate the sitemap into a string
	
	$sitemap_string = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	
	foreach ($sitemap as $sitemap_item)
	{
		$sitemap_string .= '
		<url>';
		
		if ($sitemap_item['loc'])
		{
			$sitemap_string .= '
				<loc>' . htmlentities($sitemap_item['loc']) . "</loc>";
		}
		
		if ($sitemap_item['lastmod'])
		{
			$sitemap_string .= '
				<lastmod>' . htmlentities($sitemap_item['lastmod']) . "</lastmod>";
		}
		
		if ($sitemap_item['changefreq'])
		{
			$sitemap_string .= '
				<changefreq>' . htmlentities($sitemap_item['changefreq']) . "</changefreq>";
		}
		
		if ($sitemap_item['priority'])
		{
			$sitemap_string .= '
				<priority>' . htmlentities($sitemap_item['priority']) . "</priority>";
		}
		
		$sitemap_string .= '
		</url>';
	}
	
	$sitemap_string .= '</urlset> ';
	
	// save the file
	file_put_contents("sitemap.xml", $sitemap_string);
}

function stats_count_unique_IPs()
{
	global $mydb;
	cache_set("response_distinct_ip_count", $mydb->get_response_distinct_ip_count());
}

function archive_responses()
{
	global $mydb;
	// Insert this block of code at the very top of your page: 
	
	$time = microtime(); 
	$time = explode(" ", $time); 
	$time = $time[1] + $time[0]; 
	$start = $time; 
	
	// how old do they have to be to archive?
	$time_ago = gmmktime() - (5184000);
	
	$query = "SELECT * FROM rdtom_responses WHERE Timestamp < '$time_ago' ORDER BY ID ASC LIMIT 1000";
	$results = $mydb->get_results($query);
	
	if ($results)
	{
		foreach ($results as $result_array)
		{
			// once clean, add it to the database
			$query = "	
				REPLACE INTO rdtom_responses_archive (
					ID, 
					Question_ID, 
					Answer_ID, 
					Timestamp, 
					Correct, 
					IP,
					User_ID)
				VALUES ('" . $result_array['ID'] . "',
					'" . $result_array['Question_ID'] . "', 
					'" . $result_array['Answer_ID'] . "', 
					'" . $result_array['Timestamp'] . "', 
					'" . $result_array['Correct'] . "', 
					'" . $mydb->mysql_res($result_array['IP']) . "',
					'" . $result_array['User_ID'] . "')";
			
			$mydb->run_query($query);	
			
			$query = "DELETE FROM rdtom_responses WHERE ID = " . $result_array['ID'];
			$mydb->run_query($query);
			$archive_count++;
		}
	}
	else
	{
		echo "Nothing needs archiving.<br />";
		//die;
	}
	
	// Place this part at the very end of your page 
	
	$time = microtime(); 
	$time = explode(" ", $time); 
	$time = $time[1] + $time[0]; 
	$finish = $time; 
	$totaltime = ($finish - $start); 
	
	if ($archive_count)
	{
		echo "" . $totaltime . " seconds for " . $archive_count . " items. " . ($totaltime / $archive_count) . " per item. <br />";
	} 
}

function unarchive_responses()
{
	global $mydb;
	// Insert this block of code at the very top of your page: 
	
	$time = microtime(); 
	$time = explode(" ", $time); 
	$time = $time[1] + $time[0]; 
	$start = $time; 
	
	// how old do they have to be to archive?
	$time_ago = gmmktime() - (5184000);
	
	$query = "SELECT * FROM rdtom_responses_archive WHERE Timestamp > '$time_ago' LIMIT 10";

	$results = $mydb->get_results($query);
	
	if ($results)
	{
		foreach ($results as $result_array)
		{
			// once clean, add it to the database
			$query = "	
				REPLACE INTO rdtom_responses (
					ID, 
					Question_ID, 
					Answer_ID, 
					Timestamp, 
					Correct, 
					IP,
					User_ID)
				VALUES ('" . $result_array['ID'] . "',
					'" . $result_array['Question_ID'] . "', 
					'" . $result_array['Answer_ID'] . "', 
					'" . $result_array['Timestamp'] . "', 
					'" . $result_array['Correct'] . "', 
					'" . $mydb->mysql_res($result_array['IP']) . "',
					'" . $result_array['User_ID'] . "')";
			
			$mydb->run_query($query);	
			
			$query = "DELETE FROM rdtom_responses_archive WHERE ID = " . $result_array['ID'];
			$mydb->run_query($query);
			$archive_count++;
		}
	}
	else
	{
		echo "Nothing needs archiving.<br />";
		//die;
	}
	
	// Place this part at the very end of your page 
	
	$time = microtime(); 
	$time = explode(" ", $time); 
	$time = $time[1] + $time[0]; 
	$finish = $time; 
	$totaltime = ($finish - $start); 
	
	if ($archive_count)
	{
		echo "" . $totaltime . " seconds for " . $archive_count . " items. " . ($totaltime / $archive_count) . " per item. <br />";
	} 
}
?>