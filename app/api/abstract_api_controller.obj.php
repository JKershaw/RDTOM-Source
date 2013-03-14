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
	
	public function documentation()
	{
		echo "No documentation has been written for this version, yet.";
	}
}
?>