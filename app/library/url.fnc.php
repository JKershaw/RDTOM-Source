<?php

function get_site_URL($check_if_ssl = false) {
	global $site_URL;
	if ($check_if_ssl && is_secure_https()) {
		
		// we want the https URL
		return get_secure_site_URL();
	} else {
		return $site_URL;
	}
}

function get_secure_site_URL() {
	global $site_URL;
	return str_replace("http://", "https://", $site_URL);
}

function get_http_or_https() {
	if ($_SERVER["HTTPS"] == "on") {
		return "https";
	} else {
		return "http";
	}
}

function force_secure() {
	
	// for testing, we don't care about secure when on localhost
	if ($site_URL != "http://localhost/") {
		return true;
	}
	
	// if we want to force HTTPS
	// if HTTPS is already on, everything is fine
	if ($_SERVER["HTTPS"] == "on") {
		return true;
	}
	
	// redirect to HTTPS & end script execution
	header('Location: ' . preg_replace("/http/", "https", strtolower(curPageURL()) , 1));
	exit;
}

function is_secure_https() {
	global $site_url;
	
	if ($_SERVER["HTTPS"] == "on") {
		return true;
	} else {
		return false;
	}
}