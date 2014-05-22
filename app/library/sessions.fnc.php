<?php

// get a value from the session variables
function get_session($req_array_key) {
	global $my_session;
	
	// do we have a cache of the data?
	if (!isset($my_session[$req_array_key]) && isset($_SESSION[$req_array_key])) {
		session_start();
		$my_session[$req_array_key] = $_SESSION[$req_array_key];
		session_write_close();
	}
	
	return $my_session[$req_array_key];
}

function set_session($req_array_key, $req_data) {
	global $my_session;
	
	// update the cache
	$my_session[$req_array_key] = $req_data;
	
	// we will always need to update the session
	session_start();
	$_SESSION[$req_array_key] = $req_data;
	session_write_close();
}

function delete_session($req_array_key) {
	session_start();
	$_SESSION[$req_array_key] = false;
	unset($_SESSION[$req_array_key]);
	session_write_close();
}
?>