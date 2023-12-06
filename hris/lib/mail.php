<?php

date_default_timezone_set('Asia/Jakarta');
require 'phpmailer/PHPMailerAutoload.php';

function send_mail($ADDRESS,$SUBJECT,$MESSAGE,$DEBUG = 0)
{
	$mail = new PHPMailer();
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);

	$mail->isSMTP();

	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = $DEBUG;

	$mail->Debugoutput = 'html';
	$mail->Host = get_option('SMTP_HOST'); /* tls://smtp.gmail.com:587 */
	$mail->Port = get_option('SMTP_PORT');

	//Set the encryption system to use - ssl (deprecated) or tls
	$mail->SMTPSecure = get_option('SMTP_SECURE');
	$mail->SMTPAuth = get_option('SMTP_AUTH');
	$mail->Username = get_option('SMTP_USER');
	$mail->Password = get_option('SMTP_PASS');
	$mail->setFrom(get_option('SMTP_USER'), 'CTA Job Vacancies');
	$mail->addReplyTo(get_option('SMTP_USER'), 'CTA Job Vacancies');
	
	if(is_array($ADDRESS) AND count($ADDRESS)>0){
		foreach($ADDRESS as $A){
			$mail->addAddress($A);
		}
	}
	
	$mail->Subject = $SUBJECT;
	$mail->msgHTML($MESSAGE);
	//$mail->AltBody = 'This is a plain-text message body';

	if (!$mail->send()) {
		//echo "Mailer Error: " . $mail->ErrorInfo;
		return FALSE;
	} else {
		//echo "Message sent!";
		return TRUE;
	}
}