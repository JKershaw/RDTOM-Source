<?php
function sent_auto_email($to_email, $subject, $body)
{
	global $smtp_username, $smtp_userpassword, $smtp_host, $email_from_address, $email_from_name;

	//echo  $smtp_username, $smtp_userpassword, $smtp_host;
	
	$mail = new PHPMailer();
	
	$mail->IsSMTP();                                      // set mailer to use SMTP
	
	$mail->Host = $smtp_host;  // specify main and backup server
	$mail->SMTPAuth = true;     // turn on SMTP authentication
	$mail->Username = $smtp_username;  // SMTP username
	$mail->Password = $smtp_userpassword; // SMTP password
	
	$mail->From = $email_from_address;
	$mail->FromName = $email_from_name;
	
	$mail->AddAddress($to_email);                  // name is optional
	
	$mail->IsHTML(true);  
	
	$mail->Subject = $subject;
	$mail->Body    = $body;
	
	if(!$mail->Send())
	{
		throw new exception("Message could not be sent. <p> Mailer Error: " . $mail->ErrorInfo . "</p>");
	}
}