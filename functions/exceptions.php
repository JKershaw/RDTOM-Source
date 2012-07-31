<?php

// Handle any uncaught exceptions
function exception_handler($exception) 
{
	//echo "Uncaught exception: " , $exception->getMessage(), "\n";
  
	$log_error_string = 
		"MESSAGE: [" . $exception->getMessage() . "] 
		URI: [" . $_SERVER['REQUEST_URI'] . "] 
		REQUEST: [" . print_r($_REQUEST, true) . "]";
	
	save_log("error", $log_error_string);
	
	$error_string =  $exception->getMessage();
	include("presentation/error.php");  
}

set_exception_handler('exception_handler');

?>