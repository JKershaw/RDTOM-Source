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
		$this->out_XML->addChild("api_version", "0.2");
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
			try {
				echo json_encode($this->out_XML);
				
			} catch (Exception $e) {
				echo "Something is iffy here:";
				echo $this->out_XML;
			}
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
		<h3>Version 0.2</h3>
		<p>To use the API you simply call a URI which contains the output format, resource you're after, and the parameters.</p>

		<pre>http://rollerderbytestomatic.com/api/0.2/[format]/[resource]/[parameters]</pre>
		
		<p>There are two parameters you <strong>must</strong> include:</p>
		
		<ul>
			<li><i>developer</i> - Who you are, e.g. &quot;SausageRoller&quot;</li>
			<li><i>application</i> - The name of the app calling the API (it's a good idea to include your version number), e.g. &quot;RDTOMLite_Android_0_3&quot;</li>
		</ul>
		
		<p>Failure to include these two paramters will cause your call to be rejected.</p>
		
		<p>Formats:
		
		<ul>
			<li><i>XML</i> - Default, UTF-8 XML</li>
			<li><i>NiceXML</i> - HTML formatted XML</li>
			<li><i>JSON</i> - JavaScript Object Notation</li>
			<li><i>JSONP</i> - JSON with a callback, specified using the <i>callback</i> parameter. If you want to pass an argument in your JSONP call add the parameter <i>jsonarg</i></li>
			<li><i>JavaScript</i> - A JavaScript array. You must specify the <i>var</i> parameter, which is the name of the array</li>
		</ul>
		</p>
		
		<p>Resources:
		<ul>
			<li>Question - A specific question specified using the <i>ID</i> parameter </li>
			<li>Questions - All the default questions. If you add the <i>search</i> parameter, every question in the database is searched (including those questions not in the default set) and those with the search text found in the question or answer are returned.</li>
			<li>Statistic - A given statistic specified using the <i>ID</i> parameter (available options: responses, api_calls_hourly, responses_daily, responses_hourly, responses_minutly, questions, answers, users, unique_IPs) </li>
			<li>Changes - When given the required <i>since</i> parameter (which is a Unix timestamp for a GMT date and time, i.e. PHP's gmmktime() function), a list of IDs of questions which have been updated (created or edited) and a list of questions which have been deleted are returned. Take note: questions which fall outside the default set are listed.</li>
		</ul>
		</p>
		
		<p>Example: you want to get a random question in XML, so use the following URL:</p>
		
		<pre>http://rollerderbytestomatic.com/api/0.2/xml/question/<br />?developer=SausageRoller&application=testing</pre>
		
		<p>Example: you want to get a list of changed questions since a specific time, in JSON:</p>
		
		<pre>http://rollerderbytestomatic.com/api/0.2/json/changes/<br />?developer=SausageRoller&application=testing&since=1369819830&callback=my_function</pre>
		
		
";
	}
}
