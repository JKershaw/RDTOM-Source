<?php

class api_controller extends abstract_api_controller
{
	public function main()
	{
		throw new exception ("Version 0.0 is always depreciated!", 601);
	}
	
	public function documentation()
	{
		echo "
		<h3>Version 0.0</h3>
		<p>Version 0.0 of the API is, and always has been, depreciated. It can be used to test error handling and how your app will function if the API version it is using is depreciated.
		";
	}
}