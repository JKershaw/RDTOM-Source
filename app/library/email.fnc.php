<?php
function sent_auto_email($to_email, $subject, $body) {
	global $smtp_username, $smtp_userpassword, $smtp_host, $email_from_address, $email_from_name;
	
	$mail = new PHPMailer();
	
	$mail->IsSMTP();
	
	$mail->Host = $smtp_host;
	$mail->SMTPAuth = true;
	$mail->Username = $smtp_username;
	$mail->Password = $smtp_userpassword;
	
	$mail->From = $email_from_address;
	$mail->FromName = $email_from_name;

	$mail->IsHTML(true);
	
	$mail->AddAddress($to_email);
	$mail->Subject = $subject;
	$mail->Body = $body;
	
	if (!$mail->Send()) {
		throw new exception("Message could not be sent. <p> Mailer Error: " . $mail->ErrorInfo . "</p>");
	}
}
