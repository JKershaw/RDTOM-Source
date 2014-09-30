<?php
class Email
{
	private $phpMail;
	private $host;
	private $username;
	private $userpassword;
	
	private $from_address;
	private $from_name;
	
	function __construct($phpMail) {

		global $smtp_username, $smtp_userpassword, $smtp_host, $email_from_address, $email_from_name;
		
		$this->phpMail = $phpMail;
		$this->host = $smtp_host;
		$this->username = $smtp_username;
		$this->userpassword = $smtp_userpassword;
		
		$this->from_address = $email_from_address;
		$this->from_name = $email_from_name;
	}
	
	public function send($to_email, $subject, $body) {
		$this->phpMail->IsSMTP();
		
		$this->phpMail->SMTPAuth = true;
		
		$this->phpMail->Host = $this->host;
		$this->phpMail->Username = $this->username;
		$this->phpMail->Password = $this->userpassword;
		
		$this->phpMail->From = $this->from_address;
		$this->phpMail->FromName = $this->from_name;
		
		$this->phpMail->IsHTML(true);
		
		$this->phpMail->AddAddress($to_email);
		
		$this->phpMail->Subject = $subject;
		$this->phpMail->Body = $body;
		
		if (!$this->phpMail->Send()) {
			throw new exception("Message could not be sent. <p> Mailer Error: " . $mail->ErrorInfo . "</p>");
		}
	}
}
