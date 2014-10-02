<?php
include_once __DIR__ . "/../../app/library/classes/account/ResetPasswordTokenHandler.class.php";

class ResetPasswordTokenHandlerTest extends \PHPUnit_Framework_TestCase
{
	
	public function testSendResetTokenEmail() {
		
		$fakeEmail = new fakeEmail();
		$fakeMydb = new fakeMydb();
		$forgetfulUser = new fakeUser();
		$token_string = "random";
		$fakeRandomStringGenerator = new fakeRandomStringGenerator($token_string);
		$siteURL = "http://test/";
		
		$resetPasswordTokenHandler = new ResetPasswordTokenHandler($fakeMydb, $siteURL, $fakeEmail, $fakeRandomStringGenerator);
		$resetPasswordTokenHandler->setUp($forgetfulUser);
		
		// Assert that an email was sent, so we send in a stub of an email
		$this->assertEquals($fakeEmail->sent, true);
		$this->assertEquals($fakeEmail->email_address, "testing@foo.bar");
		$this->assertEquals($fakeEmail->email_subject, "Roller Derby Test O'Matic password reset");
		$this->assertEquals($fakeEmail->email_body, "Hello, <br />
	<br />
	To reset your Roller Derby Test O'Matic account (your log-in name is bob) password, go to the following URL:<br />
	<br />
	<a href='" . $siteURL . "passwordreset/" . $token_string . "'>" . $siteURL . "passwordreset/" . $token_string . "</a> <br />
	<br />
	You can either click the link, or copy the URL into your browser's address bar.<br />
	<br />
	If you didn't request to have your password reset then you can ignore this email. If you get this email a bunch of times then something is probably not right. If you're concerned about your account's security, please get in touch via contact@rollerderbytestomatic.com.");
		
		//Assert the database was updated correctly
		$this->assertEquals($fakeMydb->token_string, $token_string);
		$this->assertEquals($fakeMydb->id, 10);
		$this->assertEquals($fakeMydb->emailAddress, "testing@foo.bar");
	}
}

class fakeEmail
{
	public $sent;
	public $email_address;
	public $email_subject;
	public $email_body;
	
	function __construct() {
		$this->sent = false;
	}
	
	public function send($email_address, $email_subject, $email_body) {
		$this->sent = true;
		$this->email_address = $email_address;
		$this->email_subject = $email_subject;
		$this->email_body = $email_body;
	}
}

class fakeUser
{
	
	public function get_ID() {
		return 10;
	}
	
	public function get_Email() {
		return "testing@foo.bar";
	}
	
	public function get_Name() {
		return "bob";
	}
}

class fakeMydb
{
	
	public $token_string;
	public $id;
	public $emailAddress;
	
	public function set_password_reset_token($token_string, $id, $emailAddress) {
		$this->token_string = $token_string;
		$this->id = $id;
		$this->emailAddress = $emailAddress;
	}
}

class fakeRandomStringGenerator
{
	private $randomString;
	
	function __construct($str) {
		$this->randomString = $str;
	}
	
	public function generate($len) {
		return $this->randomString;
	}
}
