<?php
/*
 * Support functions
 * 
 * These are the functions which are essentially misc, and often not written by me
 */

include("RandomStringGenerator");

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

function formatXmlString($xml) {  
  
  // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
  $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
  
  // now indent the tags
  $token      = strtok($xml, "\n");
  $result     = ''; // holds formatted version as it is built
  $pad        = 0; // initial indent
  $matches    = array(); // returns from preg_matches()
  
  // scan each line and adjust indent based on opening/closing tags
  while ($token !== false) : 
  
    // test for the various tag states
    
    // 1. open and closing tags on same line - no change
    if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
      $indent=0;
    // 2. closing tag - outdent now
    elseif (preg_match('/^<\/\w/', $token, $matches)) :
      $pad--;
    // 3. opening tag - don't pad this one, only subsequent tags
    elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
      $indent=1;
    // 4. no indentation needed
    else :
      $indent = 0; 
    endif;
    
    // pad the line with the required number of leading spaces
    $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
    $result .= $line . "\n"; // add to the cumulative result, with linefeed
    $token   = strtok("\n"); // get the next token
    $pad    += $indent; // update the pad size for subsequent lines    
  endwhile; 
  
  return $result;
}

function generateSalt($max = 50) {
	$randomStringGenerator = new RandomStringGenerator();
	return $randomStringGenerator->generate($max);
}
?>