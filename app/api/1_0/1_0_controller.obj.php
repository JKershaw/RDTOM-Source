<?php

class api_controller extends abstract_api_controller
{
	public function main()
	{
		// process the request and output the results
		// check if there's a client & dev name
		if (!$this->request['parameters']['developer'])
		{
			throw new exception ("Missing developer parameter.", 400);
		}
		if (!$this->request['parameters']['application'])
		{
			throw new exception ("Missing application parameter.", 400);
		}
		
		// load the resource object dynamically given the resource request
		//try 
		//{
			$resource_name = "api_resource_" . $this->request['resource'][0];
			$resource = new $resource_name($this->request['parameters']);
		//} 
		//catch (Exception $e) 
		//{
		//	throw new exception ("The resource you have requested could not be found", 404);
		//}

		// we now have the resource object, so let's start building the XML output
		
		// start the XML
		$this->out_XML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?>
		<response />');
		$this->out_XML->addChild("api_version", "1.0");
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
	
	public function output($opt_xml = false)
	{
		if ($opt_xml)
		{
			$this->out_XML = $opt_xml;
		}
		
		// output in the currect format
		if ($this->request['format'] == "nicexml")
		{
			// HTML view of XML
			echo "<pre>" . htmlentities(formatXmlString($this->out_XML->asXML())) . "</pre>";
		}
		elseif ($this->request['format'] == "json")
		{
			// JSON
			header('Content-Type: application/json');
			echo json_encode($this->out_XML);
		}
		elseif ($this->request['format'] == "jsonp")
		{
			// JSONP
			header('Content-Type: application/javascript');
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
		elseif ($this->request['format'] == "javascript")
		{
			// JSONP
			header('Content-Type: application/javascript');
			$requested_var = htmlentities($_GET['var']);
			
			if (!$requested_var)
			{
				throw new exception ("No Javascript variable name specified");
			}

			echo "var $requested_var = " . json_encode($this->out_XML) . ";";
			
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
		echo "
		<h3>Version 1.0</h3>
		<p>Development has yet to start on this version of the API.</p>
		<p>The list of features wanted for this API is as follows:</p>
		<ul>
		<li>Login/Authentication</li>
		<li>A filter and sort for questions (including complex filters based around NOT, AND, OR, as well as a way to get the default questions)</li>
		<li>Long Questions - Questions, only with all the meta data and calculated data (such as difficulty, notes, edits etc.)</li>
		<li>A way to generate and view Tests</li>
		<li>Automatic Rate limiting</li>
		<li>SSL</li>
		<li>Responses to questions can be saved</li>
		<li>Personal stats can be downloaded once logged in</li>

		</ul>
		<p>Notes on authentication</p>
		<ul>
		<li>Send Username and Password once via SSL</li>
		<li>Basic Access Authentication to get key</li>
		<li>Always use SSL</li>
		<li>Get a session key</li>
		<li>If you have no key, auto request a new one</li>
		</ul>

";
	}
}