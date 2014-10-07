<?php
class UriPath
{
	static function part($number = 0) {

		$uri = $_SERVER['REQUEST_URI'];

		$endOfPath = strpos($uri, "?");

		if (!$endOfPath) {
			$path = $uri;
		} else {
			$path = substr($uri, 0, $endOfPath);
		}

		$segments = explode("/", $path);
		return strtolower($segments[$number]);
	}
}
