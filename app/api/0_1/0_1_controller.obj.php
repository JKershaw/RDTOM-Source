<?php

class api_controller extends abstract_api_controller
{
	public function main()
	{
		// process the request and output the results
		
		// load the resource object dynamically given the resource request
		try 
		{
			if (!in_array($this->request['resource'][0], array("coffee", "question", "questions")))
			{
				throw new exception();
			}
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
		
		<p>The only Resources available are Question, and Questions. Question will return a random question (or a specific question, if you use the ID parameter). Questions will list all questions given the current defaults.</p>
";
	}
}


function formatXmlString($xml) {  
  
  // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
  $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
  
  // now indent the tags
  $token      = strtok($xml, "\n");
  $result     = ''; // holds formatted version as it is built
  $pad        = 0; // initial indent
  $matches    = array(); // returns from preg_matches()
  
  // scan each line and adjust indent based on opening/closing tags
  while ($token !== false) : 
  
    // test for the various tag states
    
    // 1. open and closing tags on same line - no change
    if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
      $indent=0;
    // 2. closing tag - outdent now
    elseif (preg_match('/^<\/\w/', $token, $matches)) :
      $pad--;
    // 3. opening tag - don't pad this one, only subsequent tags
    elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
      $indent=1;
    // 4. no indentation needed
    else :
      $indent = 0; 
    endif;
    
    // pad the line with the required number of leading spaces
    $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
    $result .= $line . "\n"; // add to the cumulative result, with linefeed
    $token   = strtok("\n"); // get the next token
    $pad    += $indent; // update the pad size for subsequent lines    
  endwhile; 
  
  return $result;
}