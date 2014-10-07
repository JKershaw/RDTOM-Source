<?php
/*
 * Support functions
 * 
 * These are the functions which are essentially misc, and often not written by me
 */

function get_average_of_array($tmp_raw_data, $float_width)
{
	
	reset($tmp_raw_data);
	$raw_data_first_key = key($tmp_raw_data);
	// make the index start at 0
	foreach ($tmp_raw_data as $tmp_raw_id => $data)
	{
		$raw_data[] = $data;
	}
	
	// add start value to make it all align
	for ($i = 0; $i < $float_width; $i++)
		$float_data_array[] = $raw_data[$i];
	
	// for each value that's not too close to the ends to average
	for ($i = $float_width; $i < count($raw_data)-$float_width; $i++)
	{
		// sum the cells to get the average
		$tmp_sum = 0;
		for ($i2 = $i - $float_width; $i2 <= $i + $float_width; $i2++)
		{
			 $tmp_sum += $raw_data[$i2];
			// $final_out .= " " . $i2 . " ";
		}
			 
			//$final_out .= $tmp_sum . " ";
		$tmp_value = round($tmp_sum/(($float_width *2) +1));
		
		$float_data_array[] = $tmp_value;
	}
	
	// add end value to make it all align
	//for ($i = count($raw_data) - $float_width; $i < count($raw_data); $i++)
	$last_value = $float_data_array[count($float_data_array) - 1];
	for ($i = 0; $i < $float_width; $i++)
		$float_data_array[] = $last_value;
	
	return $float_data_array;
}

?>