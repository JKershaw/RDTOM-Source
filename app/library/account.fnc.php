<?php

include("CookieTokenHandler");
include("Session");
include("Email");

function user_log_in($req_username, $req_password, $rememberMe = false) {
	global $mydb, $user;
	$cookieTokenHandler = new CookieTokenHandler();
	$session = new Session();
	
	// log the user in if everything is fine, setting up cookies, session vars and the like
	$user = $mydb->get_user_from_name_and_password($req_username, $req_password);
	
	if (!$user) {
		throw new exception("Name and password combination not found, please try again.");
	}
	
	// logged in
	$session->set('rdtom_userID', $user->get_ID());
	
	// does the user want to be remebered?
	if ($rememberMe) {
		
		// generate token
		$token_string = generatealphaneumericSalt(100);
		
		// save it in the database
		$mydb->add_token($token_string, $user->get_ID() , get_ip());
		
		// save it on the user's machine (last for a month)
		$cookieTokenHandler->set($token_string);
	}
}

function user_log_out() {
	global $mydb, $user;
	$cookieTokenHandler = new CookieTokenHandler();
	$session = new Session();
	
	// delete token if one exists
	if ($user) {
		$mydb->remove_token($user->get_ID() , get_ip());
	}


	$cookieTokenHandler->set("");
	
	// clear the user values
	$session->forget('rdtom_userID');
	
	$user = false;
	unset($user);
	
	forget_remebered_questions();
}

function user_sign_up($req_username, $req_password, $req_email) {
	global $mydb;
	
	$req_email = trim($req_email);
	$req_username = trim($req_username);
	
	// is the password valid?
	if (strlen($req_password) < 8) {
		throw new exception("You need to have a password which is 8 or more characters long.");
	}
	
	// is name, email and password valid? Will throw exception if not
	is_valid_username($req_username);
	
	//is the email taken?
	if ($req_email) {
		if ($mydb->is_email_taken($req_email)) {
			throw new exception("Sorry, that email address is already in use.");
		}
	}
	
	// sign up
	$mydb->add_user($req_username, $req_password, $req_email);
	
	while (!$mydb->get_user_from_name_and_password($req_username, $req_password)) {
		sleep(1);
	}
	
	return true;
}

function is_valid_username($req_username) {
	global $mydb;
	
	// is name valid?
	if (!$req_username) {
		throw new exception("You need to have a name.");
	}
	
	// is name or email taken?
	if ($mydb->is_user_name_taken($req_username)) {
		throw new exception("Sorry, that name is already in use.");
	}
	
	return true;
}

function user_update_name($req_username) {
	global $mydb, $user;
	
	if (!is_logged_in()) {
		throw new exception("You must be signed in to change your username.");
	}
	
	$req_username = trim($req_username);
	
	// is name valid? Will throw exception if not
	is_valid_username($req_username);
	
	// sign up
	$mydb->set_user_name($user->get_ID() , $req_username);
	
	// update the global object
	$user = $mydb->get_user_from_ID($user->get_ID());
}

function user_update_email($req_email) {
	global $mydb, $user;
	
	if (!is_logged_in()) {
		throw new exception("You must be signed in to change your email.");
	}
	
	$req_email = trim($req_email);
	
	//is the email taken?
	if ($req_email) {
		if ($mydb->is_email_taken($req_email)) {
			throw new exception("Sorry, that email address is already in use.");
		}
	}
	
	// sign up
	$mydb->set_user_email($user->get_ID() , $req_email);
	
	// update the global object
	$user = $mydb->get_user_from_ID($user->get_ID());
}

function user_update_password($req_oldpassword, $req_newpassword) {
	global $mydb, $user;
	
	if (!is_logged_in()) {
		throw new exception("You must be signed in to change your password.");
	}
	
	// is old password valid?
	if (!$mydb->get_user_from_name_and_password($user->get_Name() , $req_oldpassword)) {
		throw new exception("The old password you entered is not correct.");
	}
	
	// is the new password valid?
	if (strlen($req_newpassword) < 8) {
		throw new exception("You need to have a password which is 8 or more characters long.");
	}
	
	// update the password
	$mydb->set_user_password($user->get_ID() , $req_newpassword);
}

// to move to presentation file
function is_logged_in() {
	global $user;
	if ($user) {
		return true;
	}
	
	return false;
}

function set_up_reset_token($forgetful_user) {
	global $mydb;
	$email = new Email();
	
	// has a token been set up in the last 5 mins? If so, error
	
	// generate token
	$token_string = generatealphaneumericSalt(50);
	
	// save in the database
	$mydb->set_password_reset_token($token_string, $forgetful_user->get_ID() , $forgetful_user->get_Email() , get_ip());
	
	// email the user
	$reset_link = get_site_URL() . "passwordreset/" . $token_string;
	
	$email_subject = "Roller Derby Test O'Matic password reset";
	$email_body = "Hello, <br />
	<br />
	To reset your Roller Derby Test O'Matic account (your log-in name is " . $forgetful_user->get_Name() . ") password, go to the following URL:<br />
	<br />
	<a href='$reset_link'>" . $reset_link . "</a> <br />
	<br />
	You can either click the link, or copy the URL into your browser's address bar.<br />
	<br />
	If you didn't request to have your password reset then you can ignore this email. If you get this email a bunch of times then something is probably not right. If you're concerned about your account's security, please get in touch via contact@rollerderbytestomatic.com.";
	
	$email->send($forgetful_user->get_Email(), $email_subject, $email_body);
	
	// save a log
	save_log("password_reset", "User Email: " . $forgetful_user->get_Email());
}

function set_up_logged_in_user() {
	global $mydb, $user;

	$cookieTokenHandler = new CookieTokenHandler();
	$session = new Session();
	
	// do we have a session variable?
	if ($session->get('rdtom_userID')) {
		$user = $mydb->get_user_from_ID($session->get('rdtom_userID'));
	} elseif ($cookieTokenHandler->get()) {
		
		// is it valid?
		$tmp_user = $mydb->get_user_from_token($_COOKIE["token"], get_ip());
		if ($tmp_user) {
			
			// we have a valid token, so remeber the user
			$user = $tmp_user;
			$session->set('rdtom_userID', $user->get_ID());
		}
	}
}

function is_admin() {
	global $user;
	if ($user) {
		if (($user->get_Name() == "Sausage Roller") || ($user->get_Name() == "Laddie")) {
			return true;
		}
	}
	return false;
}