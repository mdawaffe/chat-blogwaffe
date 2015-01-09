<?php

defined( 'CHAT' ) or exit();

require 'functions.php';

if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) {
	authenticate();
} else if ( 'mdrchatme' !== $_SERVER['PHP_AUTH_PW'] ) {
	authenticate();
}

clear_args();
