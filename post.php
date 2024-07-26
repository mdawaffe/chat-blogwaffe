<?php

defined( 'CHAT' ) or exit();

header( 'Content-Type: application/json' );

require 'config.php';

if ( ! strlen( $_POST['message'] ) ) {
	http_response_code( 400 );
	echo json_encode( [ 'status' => 0, 'errors' => [ 'message required' ] ] );
	exit;
}

$c = curl_init( 'https://api.pushover.net/1/messages.json' );

$body = [
	'token' => $api_token,
	'user' => $user_key,
	'message' => $_POST['message'],
];

if ( isset( $_POST['title'] ) ) {
	$body['title'] = $_POST['title'];
}

if ( isset( $_FILES['attachment'] ) ) {
	$file = $_FILES['attachment'];
	switch ( $file['error'] ) {
		case UPLOAD_ERR_NO_FILE :
			break;
		case UPLOAD_ERR_OK:
			if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
				http_response_code( 400 );
				echo json_encode( [ 'status' => 0, 'errors' => [ 'Image upload error' ] ] );
				exit;
			}

			$attachment = new CURLFile( $file['tmp_name'], $file['type'], $file['name'] );

			$body['attachment'] = $attachment;
			break;
		default :
			http_response_code( 400 );
			echo json_encode( [ 'status' => 0, 'errors' => [ sprintf( 'Image upload error [%d]', $file['error'] ) ] ] );
			exit;
	}
}

curl_setopt_array( $c, array(
	CURLOPT_POST => true,
	CURLOPT_SSL_VERIFYPEER => true,
	CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
	CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
	CURLOPT_TIMEOUT => 5,
	CURLOPT_SAFE_UPLOAD => true,
	CURLOPT_POSTFIELDS => $body,
	CURLOPT_RETURNTRANSFER => true,
) );

$result = curl_exec( $c );

http_response_code( curl_getinfo( $c, CURLINFO_RESPONSE_CODE ) );
echo json_encode( $result );
