<?php

function save_log($log_name, $request_string, $question_ID = null)
{
	global $log_file_date_format, $mydb;
	
	// create the file name for the log
	$filename = "../logs/" . date($log_file_date_format) . "_" . $log_name . ".txt";

	// add meta data to the string
	$stringData = date("[d-m-Y H:i:s]") . " [" . get_ip() . "] " . $request_string . "\n";
	
	// save the log
	file_put_contents($filename, $stringData, FILE_APPEND);  
	
	// if it's a report, also save it in the database
	if ($log_name == "report")
	{
		$report = new report(-1, get_ip(), gmmktime(), $question_ID, 0, $request_string, REPORT_OPEN);
		set_report($report);
	}

}

// get the current IP address
function get_ip()
{

		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
           $ip = getenv("HTTP_CLIENT_IP");
       else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
           $ip = getenv("HTTP_X_FORWARDED_FOR");
       else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
           $ip = getenv("REMOTE_ADDR");
       else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
           $ip = $_SERVER['REMOTE_ADDR'];
       else
           $ip = "unknown";
           
	return $ip;
}

// Handle any uncaught exceptions
function exception_handler($exception) 
{
	// save a log of the error
	$log_error_string = 
		"URI: [" . $_SERVER['REQUEST_URI'] . "] 
		REQUEST: [" . print_r($_REQUEST, true) . "]
		SERVER: [" . print_r($_SERVER, true) . "]";
	
	// display an error page for the user
	$error_string = $exception->getMessage() . "<br />\n";
	$error_string .= "Line " . $exception->getLine() . " in file " . $exception->getFile() . " (Trace: " . $exception->getTraceAsString() . ")";
	
	// we don't care if it's a no-question-found error
	if (!strstr($error_string, "no question found"))
	{
		save_log("exception", $error_string . $log_error_string);
		mail("wardrox@gmail.com", "RDTOM Exception", $error_string . $log_error_string);
	}
	
	echo_error_page($error_string);
}


// Handle any uncaught Fatal Errors
function fatal_handler() 
{
	$errfile = "unknown file";
	$errstr  = "shutdown";
	$errno   = E_CORE_ERROR;
	$errline = 0;

	$error = error_get_last();

	// was there an error?
	if( $error !== NULL) {
		$errno   = $error["type"];
		$errfile = $error["file"];
		$errline = $error["line"];
		$errstr  = $error["message"];
		
		// save a log of the error
		$log_error_string = 
			"URI: [" . $_SERVER['REQUEST_URI'] . "] 
			REQUEST: [" . print_r($_REQUEST, true) . "]";
		
		// display an error page for the user
		$error_string = "\n" . $errstr . "\n";
		$error_string .= "Line " . $errline . " in file " . $errfile;
		
		@save_log("fatal", $error_string . $log_error_string);
		
		mail("wardrox@gmail.com", "RDTOM Fatal Error", $error_string . $log_error_string);
		
		echo_error_page($error_string);
	}
	
}

// error handler function
function error_handler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        $error_string = "ERROR [$errno] $errstr<br />\n";
        $error_string .= "  Fatal error on line $errline in file $errfile";
        $error_string .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        //exit(1);
        break;

    case E_USER_WARNING:
        $error_string = "WARNING [$errno] $errstr<br />\n";
        $error_string .= "Line $errline in file $errfile";
        break;

    case E_USER_NOTICE:
        $error_string = "NOTICE [$errno] $errstr<br />\n";
        $error_string .= "Line $errline in file $errfile";
        break;

    default:
        $error_string = "Unknown error type: [$errno] $errstr<br />\n";
        $error_string .= "Line $errline in file $errfile";
        break;
    }

    // save a log of the error
	$log_error_string = 
		"\nMESSAGE: [" . $error_string . "] 
		URI: [" . $_SERVER['REQUEST_URI'] . "] 
		REQUEST: [" . print_r($_REQUEST, true) . "]";
	
	save_log("error", $log_error_string);
	
	mail("wardrox@gmail.com", "RDTOM Error", $error_string . $log_error_string);
	
	// display an error page for the user
	echo_error_page($error_string);
	
    /* Don't execute PHP internal error handler */
    return true;
}

function echo_error_page($error_string)
{
	// clear the current output cache so we don't output half a page then an error
	if (ob_get_length())
	{
		ob_end_clean();
	}
	
	?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
			
			<title>Roller Derby Test O'Matic</title>
			
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
			
			<link rel="stylesheet" href="http://rollerderbytestomatic.com/css/style.css" type="text/css" />
	        <link rel="icon" href="http://rollerderbytestomatic.com/images/favicon.gif" type="image/gif"/>
	        <link rel="apple-touch-icon-precomposed" href="http://rollerderbytestomatic.com/images/RDTOM_touch_icon.png"/>	
			
			<meta name="viewport" content="width=device-width" />
			<meta name="Description" content="An online, free, Roller Derby rules test with hundreds of questions.">
	
			<script type="text/javascript">
			
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-431964-17']);
			  _gaq.push(['_trackPageview']);
			
			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
			
			</script>	
		</head>
		
		<body>
	
		<h1><a href="http://rollerderbytestomatic.com/">Roller Derby Test O'Matic</a></h1>
		<h2>Turn left and break the site.</h2>
	<?php
	if (strstr($error_string, "max_user_connections")) 
	{
	?>
		<p>Whoops! The database has just been overloaded. This is probably a temporary issue (and has been logged and will be looked into), so try doing what you just did again or refreshing the page.<p>
	<?php 
	}
	elseif (strstr($error_string, "no question found")) 
	{
	?>
		<p>Sorry, the database doesn't have the question in that you're looking for. It may have been deleted.<p>
	<?php 
	}
	elseif (strstr($error_string, "ran out of questions")) 
	{
	?>
		<p>Woah, either the site ran out of questions, or the database is being updated. Try reloading the page, or <a href="http://rollerderbytestomatic.com/forget">click here to make the site forget which questions you have answered</a>.<p>
	<?php 
	}
	else
	{
	?>
		<p>For some reason the site has generated an error. It's been logged and will be delt with accordingly (Being a broken website, Major!). You can try doing what you just did again and see if it's only a temporary bug.<p>
		<p id="p_link"><a onclick="$('#p_link').hide(); $('#p_error').show();">Click to see the error details</a></p>
		<p id="p_error" style="display:none"><?php echo $error_string; ?></p>	
	<?php 
	}
	?>
		<p style="text-align: center">
			<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2FRollerDerbyTestOMatic&amp;width=400&amp;height=558&amp;show_faces=true&amp;colorscheme=light&amp;stream=true&amp;border_color&amp;header=false&amp;appId=131848900255414" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:400px; height:558px;" allowTransparency="true"></iframe>
		</p>
		
		</body>
	</html>
	<?php 
	exit(1);
}

//set error & exception handler
set_error_handler("error_handler");
set_exception_handler('exception_handler');
register_shutdown_function( "fatal_handler" );


?>