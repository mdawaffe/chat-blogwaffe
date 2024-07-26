<?php

defined( 'CHAT' ) or exit();

function authenticate() {
	header( 'WWW-Authenticate: Basic realm="chat.blogwaffe.com"');
	header( 'HTTP/1.0 401 Unauthorized' );
	die( 'You must <a href="./">log in</a> to access this site.' );
}

function logout() {
	header( 'HTTP/1.0 401 Unauthorized' );
	die( 'You must <a href="./">log in</a> to access this site.' );
}

function back( $args = null ) {
	set_args( $args );

	header( 'Location: ./' );
	exit();
}

function cookie_path() {
	[ $cookie_path ] = explode( '?', $_SERVER['REQUEST_URI'] );
	if ( '/' !== substr( $cookie_path, -1 ) ) {
		$cookie_path = $dirname( $cookie_path );
	}

	return $cookie_path;
}

function set_args( $args ) {
	if ( ! $args ) {
		return;
	}

	[ $domain ] = explode( ':', $_SERVER['HTTP_HOST'] );

	setcookie(
		'chat',
		urlencode( json_encode( $args ) ),
		time() + 30,
		cookie_path(),
		$domain,
		isset( $_SERVER['HTTPS'] ) && 'off' !== strtolower( $_SERVER['HTTPS'] ),
		true
	);
}

function clear_args() {
	if ( ! isset( $_COOKIE['chat'] ) ) {
		return;
	}

	$_COOKIE['chat'] = json_decode( urldecode( $_COOKIE['chat'] ), true );

	[ $domain ] = explode( ':', $_SERVER['HTTP_HOST'] );

	setcookie(
		'chat',
		'.',
		time() - 31536000,
		cookie_path(),
		$domain,
		isset( $_SERVER['HTTPS'] ) && 'off' !== strtolower( $_SERVER['HTTPS'] ),
		true
	);
}

function esc_html( $string ) {
	return htmlspecialchars( $string, ENT_QUOTES, 'utf-8' );
}
