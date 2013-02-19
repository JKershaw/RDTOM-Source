<?php
abstract class api_resource
{
	protected $out_XML;
	
	function __construct($req_parameters)
	{
		
		// start the XML
		$this->out_XML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?>
		<response />');
		
		// populate the XML object with the data
		$this->build_XML($req_parameters);
	}
	
	protected function build_XML()
	{
	}
	
	public function get_XML()
	{
		return $this->out_XML;
	}
	
}
?>