<?php

class api_controller extends abstract_api_controller
{
	public function main()
	{
		// process the request and output the results
		
		// load the resource object dynamically given the resource request
		try 
		{
			$resource_name = "api_resource_" . $this->request['resource'][0];
			$resource = new $resource_name($this->request['parameters']);
		} 
		catch (Exception $e) 
		{
			throw new exception ("The resource you have requested could not be found", 404);
		}

		// we now have the resource object, so let's start building the XML output
		
		// start the XML
		$this->out_XML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?>
		<response />');
		$this->out_XML->addChild("api_version", "0.1");
		$this->out_XML->addChild("status_code", "200");
		
		// append the resource to the output
		
		// get the DOM for both
		$dom_response = dom_import_simplexml($this->out_XML);
		$dom_resource = dom_import_simplexml($resource->get_XML());
		
		// import the resource into the results document
		$dom_resource  = $dom_response->ownerDocument->importNode($dom_resource, TRUE);
		
		// append the resource into the results
		$dom_response->appendChild($dom_resource);	
			
		// display yourself
		$this->output();
	}
	
	private function output()
	{
		if ($this->request['format'] == "nicexml")
		{
			// HTML view of XML
			echo "<pre>" . htmlentities(formatXmlString($this->out_XML->asXML())) . "</pre>";
		}
		elseif ($this->request['format'] == "json")
		{
			// JSON
			echo json_encode($this->out_XML);
		}
		elseif ($this->request['format'] == "jsonp")
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
				echo "$requested_callback (" . json_encode($this->out_XML) . ", '$requested_jsonarg');";
			}
			else
			{
				echo "$requested_callback (" . json_encode($this->out_XML) . ");";
			}
		}
		else
		{
			// XML
			header('Content-Type: text/xml');
			echo $this->out_XML->asXML();
		}
	}
	
	public function documentation()
	{
		echo "Version 0.1 is the most basic of APIs. The only resouirce available is Question, which has the optional parameter of ID. If no ID is given a random qwuestion is selected. The only questions returned are those which are within the default questions given to non-logged in users.";
	}
}