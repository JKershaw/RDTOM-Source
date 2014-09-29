<?php
class Session
{
	public function set($key, $value) {
		session_start();
		$_SESSION[$key] = $value;
		session_write_close();
	}
	
	public function get($key) {
		
		session_start();
		if (isset($_SESSION[$key])) {
			$value = $_SESSION[$key];
		} else {
			$value = false;
		}
		session_write_close();
		
		return $value;
	}
	
	public function forget($key) {
		session_start();
		$_SESSION[$key] = false;
		unset($_SESSION[$key]);
		session_write_close();
	}
}
