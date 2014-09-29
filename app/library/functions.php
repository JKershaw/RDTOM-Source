<?php

include("ColourFromPercentageCalculator");

function report_question()
{
	if (!$_POST)
	{
		header( 'Location: ' . get_site_URL()) ;
		exit;
	}
	
	global $report_string, $error_string, $url_array;
	
	if ($_POST['report_question_ID'] && (strtolower(trim($_POST['report_extra'])) == "derby"))
	{
		$report_string = "Question #" . $_POST['report_question_ID'] . ": " . $_POST['report_text'];
		save_log("report", $report_string, $_POST['report_question_ID']);
		
		// clear the input
		$_POST['report_text'] = false;
		$_POST['report_question_ID'] = false;
	} 
	else 
	{
		// Your code here to handle an error
		if (!(strtolower(trim($_POST['report_extra'])) == "derby"))
		{
			$error_string = "The anti-spam code wasn't entered correctly. Please try it again.";
		}
		else
		{
			$error_string = "Sorry, and error has occured. Please try again";
		}
		
		// return to the question
		$url_array[0] = "question";
		$url_array[1] = $_POST['report_question_ID'];
	}
}


function forget_remebered_questions()
{
	delete_session('random_questions_results');
	delete_session('random_questions_asked');
}

function get_colour_from_percentage($perc_value)
{
	$ColourFromPercentageCalculator = new ColourFromPercentageCalculator();
	return $ColourFromPercentageCalculator->calculate($perc_value);
}

function compare_questions($req_question1, $req_question2)
{
	return strnatcasecmp($req_question1->get_Section(), $req_question2->get_Section());
}

// taken from http://www.zachstronaut.com/posts/2009/01/20/php-relative-date-time-string.html
function time_elapsed_string($ptime) {
    $etime = gmmktime() - $ptime;
    
    if ($etime < 10) {
        return 'just now';
    }
    
    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
                );
    
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '') . " ago";
        }
    }
}

function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
    $url = get_http_or_https() . '://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
?>