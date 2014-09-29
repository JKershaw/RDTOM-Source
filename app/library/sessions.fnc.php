<?php

// get a value from the session variables
function get_session($key) {
	global $my_session;
	
	// do we have a cache of the data?
	if (!isset($my_session[$key]) && isset($_SESSION[$key])) {
		session_start();
		$my_session[$key] = $_SESSION[$key];
		session_write_close();
	}
	
	return $my_session[$key];
}

function set_session($key, $req_data) {
	global $my_session;
	
	// update the cache
	$my_session[$key] = $req_data;
	
	// we will always need to update the session
	session_start();
	$_SESSION[$key] = $req_data;
	session_write_close();
}

function delete_session($key) {
	session_start();
	$_SESSION[$key] = false;
	unset($_SESSION[$key]);
	session_write_close();
}
?>