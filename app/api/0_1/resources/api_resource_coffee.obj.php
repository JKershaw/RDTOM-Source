<?php
class api_resource_coffee extends api_resource
{
	protected function build_XML($parameters)
	{
		throw new exception("The server is a teapot and can not serve your request.", 418);
	}
}
?>