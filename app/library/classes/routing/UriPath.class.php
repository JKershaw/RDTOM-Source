<?php
class UriPath
{
	static function part($number) {
		
		$uri = $_SERVER['REQUEST_URI'];
		
		$path = self::getPath($uri);
		
		$segmentArray = self::getSegmentArray($path);
		
		if (isset($segmentArray[$number])) {
			return $segmentArray[$number];
		} else {
			return false;
		}
	}
	
	private static function getPath($uri) {
		
		$endOfPath = strpos($uri, "?");
		
		if (!$endOfPath) {
			$path = $uri;
		} else {
			$path = substr($uri, 0, $endOfPath);
		}

		return $path;
	}

	private static function getSegmentArray($path) {
		$segmentArray = array();
		
		if (strpos($path, "/") !== false) {
			foreach (explode("/", $path) as $segment) {
				if (trim($segment)) {
					$segmentArray[] = strtolower($segment);
				}
			}
		} else {
			$segmentArray[] = strtolower($path);
		}

		return $segmentArray;
	}

	public static function pathArray() {
		$uri = $_SERVER['REQUEST_URI'];
		
		$path = self::getPath($uri);
		
		return self::getSegmentArray($path);
	}
}
