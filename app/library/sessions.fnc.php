<?php

include("Session");

// get a value from the session variables
function get_session($key) {
	$session = new Session();
	return $session->get($key);
}

function set_session($key, $value) {
	$session = new Session();
	$session->set($key, $value);
}

function delete_session($key) {
	$session = new Session();
	return $session->forget($key);
}
?>