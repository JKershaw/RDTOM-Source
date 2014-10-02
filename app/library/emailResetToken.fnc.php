<?php
include ("EmailResetTokenHandler");

function set_up_reset_token($forgetfulUser) {

	global $mydb;
	$siteURL = get_site_URL();

	$emailResetTokenHandler = new EmailResetTokenHandler($mydb, $siteURL);
	
	$token = generatealphaneumericSalt(50);

	$emailResetTokenHandler->sendPasswordResetToken($forgetfulUser, $token);
	
}