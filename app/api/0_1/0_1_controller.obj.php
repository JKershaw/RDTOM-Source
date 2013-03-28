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
		echo "
		<h3>Version 0.1</h3>
		<p>To use the API you simply call a URI which contains the output format, resource you're after, and any parameters.</p>

		<pre>http://rollerderbytestomatic.com/api/0.1/[format]/[resource]/[parameters]</pre>
		
		<p>If, for example, you want to get a random question in XML, you would use the following URL:</p>
		
		<pre>http://rollerderbytestomatic.com/api/0.1/xml/question/</pre>
		
		<p>In another example, if you wanted to get the results in JSONP, you must specify the callback function. To do this, add the parameter \"Callback\". In the following example we also want a specific question, so a second parameter of \"ID\" is added which specifies the question's ID.</p>
		
		<pre>http://rollerderbytestomatic.com/api/0.1/jsonp/question/?callback=my_function&ID=704</pre>
		
		<p>Available Formats:
		
		<ul>
			<li>XML (Default)</li>
			<li>NiceXML</li>
			<li>JSON</li>
			<li>JSONP</li>
		</ul>
		</p>
		
		<p>NiceXML is HTML formatted XML, useful for debugging.</p>
		
		<p>Parameters you can add:
		<ul>
			<li>jsonarg - if you want to pass an argument in your JSONP call</li>
			<li>callback - the name of your callback function, required if you're requesting JSONP</li>
			<li>ID - Used when requesting a Question to specify the ID</li>
		</ul>
		</p>
		
		<p>The only Resource available is Question.</p>
";
	}
}