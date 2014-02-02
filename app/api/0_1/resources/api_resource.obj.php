<?php
abstract class api_resource
{
	protected $resource_XML;
	
	function __construct($req_parameters)
	{
		$this->resource_XML = new SimpleXMLElement('<resource />');
		
		// populate the XML object with the data
		$this->build_XML($req_parameters);
	}
	
	protected function build_XML()
	{
	}
	
	public function get_XML()
	{
		return $this->resource_XML;
	}
	
}
?>