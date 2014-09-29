<?php
class Session
{
	private $sessionCache;

	public function set($key, $value) {
		session_start();
		$_SESSION[$key] = $value;
		$this->sessionCache[$key] = $value;
		session_write_close();
	}
	
	public function get($key) {

		if (isset($this->sessionCache[$key])) {
			return $this->sessionCache[$key];
		}
		
		session_start();
		if (isset($_SESSION[$key])) {
			$value = $_SESSION[$key];
		} else {
			$value = null;
		}
		session_write_close();
		
		return $value;
	}
	
	public function forget($key) {
		session_start();
		unset($_SESSION[$key]);
		unset($this->sessionCache[$key]);
		session_write_close();
	}
}
