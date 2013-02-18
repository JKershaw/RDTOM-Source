<?php
/*
 * If the URL or URI requested is rollerderbytestomatic.com/API/[anything] this file is included
 * It needs to handle all API related items.
 * 
 * Documentation is a Web request, so is included in the presentation folder.
 * 
 * RDTOM.com/API	/[version]	/[format]	/[resource]
 * 			/0		/1			/2			/3
 */
if ($url_array[1] == "0.1")
{
	// version 0.1 has been requested
	
	// get the resource requested
	$resource_name = $url_array[3];
	
	$out_XML = get_resource_xml($resource_name);
	
	// output the resource
	output_xml($out_XML, $url_array[2]);
}
else
{
	// TODO error handling
	echo "The API version you have requested does not exist.";
}

function get_resource_xml($resource_name)
{
	// for each resource, load up the object
	switch ($resource_name) 
	{	
		case "question":
			$parameters = Array(
				"resource" => "question",
				"ID" => $_GET['ID']);
			
			$api_resource = new api_resource_question($parameters);
			
			break;	
		default:
			//throw new Exception ("Resource not found: " . htmlentities($resource_name));
			echo "Resource not found";
			exit;
			break;
	}
	
	return $api_resource->get_XML();
}

function output_xml($out_XML, $format)
{
	if ($format == "nicexml")
	{
		// HTML view of XML
		echo "<pre>" . htmlentities(formatXmlString($out_XML->asXML())) . "</pre>";
	}
	elseif ($format == "json")
	{
		// JSON
		echo json_encode($out_XML);
	}
	elseif ($format == "jsonp")
	{
		// JSONP
		$requested_callback = htmlentities($_GET['callback']);
		$requested_jsonarg = htmlentities($_GET['jsonarg']);
		
		if (!$requested_callback)
		{
			throw new exception ("No JSONP callback specified");
		}
		
		if (!$requested_jsonarg)
		{
			echo "$requested_callback (" . json_encode($out_XML) . ", '$requested_jsonarg');";
		}
		else
		{
			echo "$requested_callback (" . json_encode($out_XML) . ");";
		}
	}
	else
	{
		// XML
		header('Content-Type: text/xml');
		echo $out_XML->asXML();
	}
}

?>