<?php
require 'app-load.php';

if( send_mail(array(get_option('SMTP_TESTER')),'SMTP Sending Tes', '<p>Hi, this email is testing only, please remove me if you want.</p>') )
{
	echo 'Email sent to '.get_option('SMTP_TESTER');
}
else
{
	echo 'Email not send.';
}

?>