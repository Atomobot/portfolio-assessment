<?php

if( !empty( $_POST ) ) {
	$data = array();
	foreach( $_POST as $key => $value ) {
		$data[$key] = make_safe( $value );
	}
	$result = sendMail( 'Website message from' .$data['name'], 'smtp.gmail.com', 'not@anaddress.com', 'notapassword', '', $data['message'], 'pc@atomobot.se', $data['email'], $data['email'] );
	if( $result === TRUE ) { echo 'Thanks for your message'; }
	else { echo 'Oops! Please try again later<br />'; print_r( $result ); }
} else {
	echo 'Naughty Naughty';
}

/**
 * Sends mail by connecting to an smtp server
 * 
 * @param string $subject
 * @param string $smtp_server
 * @param string $smtp_username
 * @param string $smtp_password
 * @param string $html html portion of the message body
 * @param string $text text portion of the message body
 * @param string|array $to
 * @param string $from
 * @param array $cc
 * @param array $bcc
 * @return boolean|string
 */
function sendMail( $subject, $smtp_server, $smtp_username, $smtp_password, $html, $text, $to, $from, $reply_to, $cc = array(), $bcc = array() ) {
	require_once 'Mail.php'; // require pear classes
	require_once 'Mail/mime.php';
	$to = is_array( $to ) ? $to : explode( ',', $to ); // create the headers
	$headers = array( 'Subject' => $subject, 'From' => $from, 'Reply-To' => $reply_to, 'To' => implode( ',', $to ) ); // end $headers
	$mime = new Mail_mime( "\n" ); // create the message
	$mime->addCc( implode( ',', $cc ) );
	$mime->addBcc( implode( ',', $bcc ) );
	$mime->setTXTBody( $text );
	//$mime->setHTMLBody( $html );
	$body = $mime->get(); // always call these methods in this order
	$headers = $mime->headers( $headers );
	$smtp_params = array( 'host' => $smtp_server, 'port' => 587, 'auth' => TRUE, 'username' => $smtp_username, 'password' => $smtp_password, 'timeout' => 20, 'localhost' => $_SERVER['SERVER_NAME'] ); // end $smtp_params
	$smtp = Mail::factory( 'smtp', $smtp_params ); // create the smtp mail object
	$recipients = array_merge( $to, $cc, $bcc );
	$mail = $smtp->send( $recipients, $headers, $body ); // send the message
	error_reporting( E_ERROR | E_PARSE ); // set error level
	if( PEAR::isError( $mail ) ) { return $mail->getMessage(); } // end if there was an error
	return TRUE;
}

function make_safe( $value ) {
	$value = stripslashes( $value );
	$value = htmlentities( $value, ENT_QUOTES, "UTF-8" );
	return $value;
}