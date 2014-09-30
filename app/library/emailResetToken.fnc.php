<?php
include ("Email");

//TODO make this whole thing a new class; TokenResetHandler

function set_up_reset_token($forgetful_user) {
	global $mydb;
	
	$token_string = generatealphaneumericSalt(50);
	
	$mydb->set_password_reset_token($token_string, $forgetful_user->get_ID() , $forgetful_user->get_Email() , get_ip());
	
	sendTokenResetEmail($token_string, $forgetful_user);
	
	save_log("password_reset", "User Email: " . $forgetful_user->get_Email());
}

function sendTokenResetEmail($token_string, $forgetful_user){
	$email = new Email();

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
	
	$email->send($forgetful_user->get_Email() , $email_subject, $email_body);
}