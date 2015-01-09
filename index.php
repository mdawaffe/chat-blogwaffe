<?php

define( 'CHAT', true );

require 'init.php';

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	require 'post.php';
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ping</title>

	<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/jumbotron-narrow.css" rel="stylesheet">
</head>
<body>
	<div class="container">
		<header class="header">
			<nav>
				<ul class="nav nav-pills pull-right">
					<li class="danger"><a href="./logout.php">Logout</a></li>
				</ul>
			</nav>
			<h3 class="text-muted">Welcome to Ping, <?php echo esc_html( $_SERVER['PHP_AUTH_USER'] ); ?></h3>
		</header>

<?php	if ( isset( $_COOKIE['chat'] ) && isset( $_COOKIE['chat']['sent'] ) ) :
		if ( 1 === $_COOKIE['chat']['sent'] ) {
			$class = 'success';
			$message = 'Message sent.';
		} else {
			$class = 'danger';
			$message = 'Message could not be sent.';
		}
?>

		<p class="notice bg-<?php echo esc_html( $class ); ?>"><?php echo esc_html( $message ); ?></p>

<?php	endif; ?>

		<div class="jumbotron">
			<form action="." method="POST">
				<div class="form-group">
					<label class="sr-only" for="message">Message</label>
					<textarea class="form-control input-lg" rows="2" id="message" name="message" placeholder="Type your message&hellip;"></textarea>
				</div>
				<input type="submit" class="btn btn-primary" value="Ping!" />
			</form>
		</div>

		<footer class="footer">
			<p>&copy; mdawaffe <?php echo esc_html( gmdate( 'Y' ) ); ?></p>
		</footer>
	</div>
</body>
</html>
