<?php

// Handle any uncaught exceptions
function exception_handler($exception) 
{
	// save a log of the error
	$log_error_string = 
		"MESSAGE: [" . $exception->getMessage() . "] 
		URI: [" . $_SERVER['REQUEST_URI'] . "] 
		REQUEST: [" . print_r($_REQUEST, true) . "]";
	
	save_log("exception", $log_error_string);
	
	// display an error page for the user
	echo_error_page($exception->getMessage());
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
        $error_string .= "ERROR [$errno] $errstr<br />\n";
        $error_string .= "  Fatal error on line $errline in file $errfile";
        $error_string .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        //exit(1);
        break;

    case E_USER_WARNING:
        $error_string .= "WARNING [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        $error_string .= "NOTICE [$errno] $errstr<br />\n";
        break;

    default:
        $error_string .= "Unknown error type: [$errno] $errstr<br />\n";
        break;
    }

    // save a log of the error
	$log_error_string = 
		"MESSAGE: [" . $error_string . "] 
		URI: [" . $_SERVER['REQUEST_URI'] . "] 
		REQUEST: [" . print_r($_REQUEST, true) . "]";
	
	save_log("error", $log_error_string);
	
	// display an error page for the user
	echo_error_page($error_string);
	
    /* Don't execute PHP internal error handler */
    return true;
}

function echo_error_page($error_string)
{
	
	?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
			
			<title>Roller Derby Test O'Matic</title>
			
			<link rel="stylesheet" href="http://rollerderbytestomatic.com/presentation/style.css" type="text/css" />
	        <link rel="icon" href="http://rollerderbytestomatic.com/presentation/favicon.gif" type="image/gif"/>
	        <link rel="apple-touch-icon-precomposed" href="http://rollerderbytestomatic.com/presentation/RDTOM_touch_icon.png"/>	
			
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
	
		<p>For some reason the site has generated an error. It's been logged and will be delt with accordingly (Being a broken website, Major!). You can try doing what you just did again and see if it's only a temporary bug.<p>
		<p>The error was:</p>
		<pre><?php echo $error_string; ?></pre>
		</body>
	</html>
	<?php 
	exit(1);
}

//set error & exception handler
set_error_handler("error_handler");
set_exception_handler('exception_handler');

?>