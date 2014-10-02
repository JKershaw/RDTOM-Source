<?php
include ("ResetPasswordTokenHandler");

function set_up_reset_token($forgetfulUser) {

	global $mydb;

	$resetPasswordTokenHandler = new ResetPasswordTokenHandler($mydb);
	$resetPasswordTokenHandler->handle($forgetfulUser);
	
}