<?php

defined( 'CHAT' ) or exit();

require 'config.php';

if ( ! strlen( $_POST['message'] ) ) {
	back();
}

$c = curl_init( 'https://api.pushover.net/1/messages.json' );

curl_setopt_array( $c, array(
	CURLOPT_POST => true,
	CURLOPT_SSL_VERIFYPEER => true,
	CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
	CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
	CURLOPT_TIMEOUT => 5,
	CURLOPT_POSTFIELDS => http_build_query( array(
		'token' => $api_token,
		'user' => $user_key,
		'message' => $_POST['message'],
	) ),
) );

$result = curl_exec( $c );

back( array(
	'sent' => (int) $result,
) );
