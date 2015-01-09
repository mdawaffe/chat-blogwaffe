<?php

define( 'CHAT', true );

require 'functions.php';

if ( isset( $_SERVER['PHP_AUTH_USER'] ) ) {
	logout();
} else {
	back();
}
