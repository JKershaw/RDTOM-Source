<?php

class api_controller extends abstract_api_controller
{
	public function main()
	{
		// get the XML of the requested resource and save in the local $out_XML
		switch ($this->request['resource'][0]) 
		{	
			case "question":
				$this->load_resource_question();
				break;	
			case "questions":
				throw new exception ("Questions will be added soon!", 501);
				break;		
			case "test":
				throw new exception ("The Test resource will be added soon!", 501);
				break;		
			case "stat":
				throw new exception ("Statistics will be added soon!", 501);
				break;	
			case "stats":
				throw new exception ("Statistics will be added soon!", 501);
				break;	
			case "coffee":
				$this->load_resource_coffee();
				break;	
			default:
				throw new exception ("The resource you have requested could not be found", 404);
				exit;
				break;
		}
		
		// display yourself
		$this->output();
	}
	
	private function load_resource_question()
	{
		// get the resource needed
		// start the XML
		$this->out_XML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?>
		<response />');
		$this->out_XML->addChild("api_version", "0.1");
		$this->out_XML->addChild("status_code", "200");
		
		// will throw na exception if not 200
		$resource_question = new api_resource_question($this->request['parameters']);

		// append the resource to the output
		
		// get the DOM for both
		$dom_response = dom_import_simplexml($this->out_XML);
		$dom_resource = dom_import_simplexml($resource_question->get_XML());
		
		// import the resource into the results document
		$dom_resource  = $dom_response->ownerDocument->importNode($dom_resource, TRUE);
		
		// append the resource into the results
		$dom_response->appendChild($dom_resource);
		
		
		//$this->out_XML = $resource_question->get_XML();
	}
	
	private function load_resource_coffee()
	{
		$resource_coffee = new api_resource_coffee($this->request['parameters']);
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
		echo "Version 0.1 is the most basic of APIs.";
	}
}