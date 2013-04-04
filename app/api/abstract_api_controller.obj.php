<?php
abstract class abstract_api_controller
{
	protected $request;
	protected $out_XML;
	
	function __construct($req_request)
	{
		$this->request = $req_request;
	}
	
	public function main()
	{
		echo "Main function";
	}
	
	public function output($opt_xml = false)
	{
		//Output the data, or a passed XML error
		header('Content-Type: text/xml');
		if ($opt_xml)
		{
			echo $opt_xml->asXML();
		}
		else
		{
			echo $this->out_XML->asXML();
		}
	}
	
	public function documentation()
	{
		echo "No documentation has been written for this version, yet.";
	}
}
?>