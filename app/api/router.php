<?php
/*
 * If the URL or URI requested is rollerderbytestomatic.com/API/[anything] this file is included
 * It needs to handle all API related items.
 * 
 * Documentation is a Web request, so is included in the presentation folder.
 * 
 * RDTOM.com/API	/[version]	/[format]	/[resource]	/...
 * 			/0		/1			/2			/3			/...
 */

/*
 * Status codes follow standard HTTP status codes
 * Extended into the 600s for specific errors
 */

/*
 * Complex queries vias XML http://stackoverflow.com/questions/10049677/how-to-model-logical-boolean-expressions-in-xml-efficiently
 */

$api_status_codes = array(
	// everything's OK
	200 => array(
		"text" => "OK",
		"description" => "Request sucessful"),
	
	// General error
	400 => array(
		"text" => "Bad Request",
		"description" => "The request could not be fullfilled due to bad syntax"),
	404 => array(
		"text" => "Resource Not Found",
		"description" => "The resource requested could not be found"),
	416 => array(
		"text" => "ID not found",
		"description" => "A request for something with an ID not found was requested"),
	418 => array(
		"text" => "I am a teapot",
		"description" => "The server is now using Hyper Text Coffee Pot Control Protocol"),
	420 => array(
		"text" => "Rate Limited",
		"description" => "Calm down. Request failed due to too many requests recently."),
	424 => array(
		"text" => "Method Failure",
		"description" => ""),
	451 => array(
		"text" => "Unavailable For Legal Reasons",
		"description" => ""),
	
	// Server errors
	500 => array(
		"text" => "Internal Server Error",
		"description" => "Something went wrong."),
	501 => array(
		"text" => "Not Implemented - Yet",
		"description" => "It's coming, but be patient"),
	503 => array(
		"text" => "Service Unavailable",
		"description" => "The server is down for maintenance, please try again in a moment"),
	
	// Specific RDTOM API Errors
	600 => array(
		"text" => "API Version Incorrect",
		"description" => "The API version you have requested does not exist"),
	601 => array(
		"text" => "API Depreciated",
		"description" => "The API version you have requested is no longer supported")
	
	);


if ($url_array[1])
{
	// save a log of the API request
	save_log("api", $_SERVER['REQUEST_URI']);
	
	// save a cache of the api request
	$cached_api_calls = cache_get("api_calls");
	
	// delete old api calls
	if ($cached_api_calls)
	{
		foreach ($cached_api_calls as $id => $cached_api_call)
		{
			if ($cached_api_call['timestamp'] < gmmktime() - 3600)
			{
				unset($cached_api_calls[$id]);
			}
		}
	}
	
	// save a new api call to the cache
	$cached_api_calls[] = Array("timestamp" => gmmktime(), "request" => $_SERVER['REQUEST_URI']);

	cache_set("api_calls", $cached_api_calls);
	
	// process the request
	try 
	{
		// get the target controller
		$target = "../app/api/" . str_ireplace(".", "_", $url_array[1]) . "/" . str_ireplace(".", "_", $url_array[1]) . "_controller.obj.php";
		
		//get target
		if (file_exists($target))
		{
			include_once("../app/api/abstract_api_controller.obj.php");
			
		    include_once($target);
		    
		    $resource_array = Array();
		     
		    for($i = 3; $i < count($url_array); $i++)
		    {
		    	$resource_array[] = $url_array[$i];
		    }
		    
			$request = Array(
				"format" => $url_array[2],
				"resource" => $resource_array,
				"parameters" => $_GET);
		
			// make a new controller
		    $controller = new api_controller($request);

		    if ($resource_array[0] == "documentation")
		    {
		    	// get the controller's documentaion
			    $controller->documentation();
		    }
		    else 
		    {
			    // activate the controller
			    $controller->main();
		    }
		
		}
		else
		{
		    //can't find the file in 'controllers'! 
		    throw new exception('The controller for this version number was not found', 600);
		}
	} 
	catch (Exception $e) 
	{
		// give a formatted XML error
		$out_XML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?>
		<response />');
		$out_XML->addChild("status_code", $e->getCode());
		$out_XML->addChild("status_code_text", $api_status_codes[$e->getCode()]['text']);
		$out_XML->addChild("status_code_description", $api_status_codes[$e->getCode()]['description']);
		$out_XML->addChild("status_message", $e->getMessage());
		
		// try using the controller's output function
		try 
		{
			if (!$controller)
			{
				throw new exception();
			}
			$controller->output($out_XML);
		} 
		catch (Exception $e) 
		{
			// worse case, just output the XML
			header('Content-Type: text/xml');
			echo $out_XML->asXML();
		}
		
	}
}
else
{
	include_once("../app/presentation/apidocumentation.php");
}

function api_resources_autoload($resource_name)
{
	global $url_array;
	$target = "../app/api/" . str_ireplace(".", "_", $url_array[1]) . "/resources/" . $resource_name . ".obj.php";
	return $target;
	
}
?>