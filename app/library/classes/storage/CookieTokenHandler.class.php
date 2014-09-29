<?php 
class CookieTokenHandler
{
	public function set($token_string) {
		setcookie("token", $token_string, gmmktime() + 2678400);
	}

	public function get() {
		return $_COOKIE["token"];
	}
}