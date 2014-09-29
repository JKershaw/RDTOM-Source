<?php

function add_google_chart_drawChart($req_script) {
	global $drawChart_script_array;
	$drawChart_script_array[] = $req_script;
}

function get_google_chart_script() {
	global $drawChart_script_array;
	if ($drawChart_script_array) {
		$script_string = implode("\n", $drawChart_script_array);
		
		$script.= '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
		$script.= '<script type="text/javascript">
	   	
	   		var options_user_section_totals;
	   		var data_user_section_totals;
	   		var data_stats_user_progress;
	   		var options_stats_user_progress;
	   		
	      google.load("visualization", "1", {packages:["corechart"]});
	      google.setOnLoadCallback(drawChart);
	      function drawChart() {
	        ' . $script_string . '
	      }
	    </script>';
	}
	return $script;
}