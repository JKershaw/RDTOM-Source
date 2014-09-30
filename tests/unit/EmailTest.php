<?php
include_once __DIR__ . "/../../app/library/classes/email/Email.class.php";

class EmailTest extends \PHPUnit_Framework_TestCase
{
	
	public function testSendEmail() {
		
		$smtp_username = "username";
		$smtp_userpassword = "password";
		$smtp_host = "host";

		$email_from_address = "from@address.com";
		$email_from_name = "from name";
		
		$phpMail = new PHPMailMock();
		
		$email = new Email($phpMail, $smtp_username, $smtp_userpassword, $smtp_host, $email_from_address, $email_from_name);
		
		$to_email = "to@email.com";
		$subject = "Subject";
		$body = "body";
		
		$email->send($to_email, $subject, $body); 

		$this->assertEquals($phpMail->isSMTP, true);

		$this->assertEquals($phpMail->Host, $smtp_host);
		$this->assertEquals($phpMail->Username, $smtp_username);
		$this->assertEquals($phpMail->Password, $smtp_userpassword);

		$this->assertEquals($phpMail->SMTPAuth, true);

		$this->assertEquals($phpMail->From, $email_from_address);
		$this->assertEquals($phpMail->FromName, $email_from_name);

		$this->assertEquals($phpMail->isHTML, true);

		$this->assertEquals($phpMail->AddedAddress, $to_email);

		$this->assertEquals($phpMail->Subject, $subject);
		$this->assertEquals($phpMail->Body, $body);
	}
}

class PHPMailMock
{
	public $isSMTP;
	public $isHTML;

	public $Host;
	public $SMTPAuth;
	public $Username;
	public $Password;

	public $From;
	public $FromName;

	public $AddedAddress;

	public $Subject;
	public $Body;

	public $HasBeenSent;

	function __construct() {
		$this->isSMTP = false;
		$this->HasBeenSent = false;
	}

	public function IsSMTP() {
		$this->isSMTP = true; 
	}

	public function IsHTML($isHTML) {
		$this->isHTML = $isHTML; 
	}

	public function AddAddress($address) {
		$this->AddedAddress = $address; 
	}

	public function Send() {
		$this->HasBeenSent = true; 
		return true;
	}
}
