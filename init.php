<?php

defined( 'CHAT' ) or exit();

require 'functions.php';
require 'config.php';

if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) {
	authenticate();
} else if ( $password !== $_SERVER['PHP_AUTH_PW'] ) {
	authenticate();
}

clear_args();
