<?php
include ("CookieTokenHandler");
include ("Session");

function user_log_in($req_username, $req_password, $rememberMe = false) {
	global $mydb;
	$cookieTokenHandler = new CookieTokenHandler();
	$session = new Session();
	
	// log the user in if everything is fine, setting up cookies, session vars and the like
	set_global_user($mydb->get_user_from_name_and_password($req_username, $req_password));

	$user = get_global_user();
	
	if (!$user) {
		throw new exception("Name and password combination not found, please try again.");
	}
	
	// logged in
	$session->set('rdtom_userID', $user->get_ID());
	
	// does the user want to be remebered?
	if ($rememberMe) {
		
		// generate token
		$token_string = generateSalt(100);
		
		// save it in the database
		$mydb->add_token($token_string, get_global_user()->get_ID() , get_ip());
		
		// save it on the user's machine (last for a month)
		$cookieTokenHandler->set($token_string);
	}
}

function user_log_out() {
	global $mydb;
	$cookieTokenHandler = new CookieTokenHandler();
	$session = new Session();
	$user = get_global_user();
	
	// delete token if one exists
	if ($user) {
		$mydb->remove_token($user->get_ID() , get_ip());
	}
	
	$cookieTokenHandler->set("");
	
	// clear the user values
	$session->forget('rdtom_userID');
	
	unset_global_user();
	
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
	global $mydb;
	$user = get_global_user();
	
	if (!is_logged_in()) {
		throw new exception("You must be signed in to change your username.");
	}
	
	$req_username = trim($req_username);
	
	// is name valid? Will throw exception if not
	is_valid_username($req_username);
	
	// sign up
	$mydb->set_user_name($user->get_ID() , $req_username);
	
	// update the global object
	set_global_user($mydb->get_user_from_ID($user->get_ID()));
}

function user_update_email($req_email) {
	global $mydb;
	$user = get_global_user();
	
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
	set_global_user($mydb->get_user_from_ID($user->get_ID()));
}

function user_update_password($req_oldpassword, $req_newpassword) {
	global $mydb;
	$user = get_global_user();
	
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
	$user = get_global_user();
	if ($user) {
		return true;
	}
	
	return false;
}

function set_up_logged_in_user() {
	global $mydb;
	
	$cookieTokenHandler = new CookieTokenHandler();
	$session = new Session();
	
	// do we have a session variable?
	if ($session->get('rdtom_userID')) {
		set_global_user($mydb->get_user_from_ID($session->get('rdtom_userID')));
	} elseif ($cookieTokenHandler->get()) {
		
		// is it valid?
		$tmp_user = $mydb->get_user_from_token($_COOKIE["token"], get_ip());
		if ($tmp_user) {
			
			// we have a valid token, so remeber the user
			set_global_user($tmp_user);
			$user = get_global_user();
			$session->set('rdtom_userID', $user->get_ID());
		}
	}
}

function is_admin() {
	$user = get_global_user();
	if ($user) {
		if (($user->get_Name() == "Sausage Roller") || ($user->get_Name() == "Laddie")) {
			return true;
		}
	}
	return false;
}

function set_global_user($userToSet){
	global $user;
	$user = $userToSet;
}

function unset_global_user(){
	global $user;
	$user = false;
	unset($user);
}

function get_global_user(){
	global $user;
	return $user;
}

