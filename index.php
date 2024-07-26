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
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Send Notification</title>

	<link href="assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
	<style>
		#feedback > *:first-child {
			margin-top: 0.25em;
		}

		#feedback {
			pointer-events: none;
		}

		#feedback:after {
			display: block;
			content: "\274E";
			position: absolute;
			top: 1rem;
			right: 1rem;
			pointer-events: auto;
			cursor: pointer;
		}

		fieldset {
			display: contents;
		}

		fieldset .composing {
			display: initial;
		}

		fieldset .sending {
			display: none;
		}

		fieldset:disabled .composing {
			display: none;
		}

		fieldset:disabled .sending {
			display: initial;
		}

		output:has(p:empty):has(h2:empty):has(img[src=""]) {
			display: none;
		}

		output {
			border-top: 1px solid #ddd;
		}
	</style>
</head>
<body>
	<div class="container my-5 w-50">
		<nav class="navbar navbar-expand-lg">
			<div class="container-fluid p-0">
				<span class="navbar-brand mb-0 h1">Welcome, <?php echo esc_html( $_SERVER['PHP_AUTH_USER'] ); ?></span>
				<span class="navbar-nav"><a class="btn btn-danger" href="./logout.php">Logout</a></span>
			</div>
		</nav>

		<div id="feedback" class="alert d-none" role="alert"></div>

		<div class="bg-light p-3">
			<form action="." method="POST" enctype="multipart/form-data"><fieldset>
				<div class="mb-3">
					<label class="form-label" for="title">Title (optional)</label>
					<input class="form-control" id="title" name="title" maxlength="250" />
				</div>
				<div class="mb-3">
					<label class="form-label" for="message">Message</label>
					<textarea class="form-control" rows="2" id="message" name="message" placeholder="Type your message&hellip;" required maxlength="1024"></textarea>
				</div>
				<div class="mb-3">
					<label for="attachment" class="form-label">Image (optional)</label>
					<input class="form-control" type="file" id="attachment" name="attachment" accept="image/*"> <!-- /* */ -->
				</div>
				<p>
					<button type="submit" class="btn btn-primary"><span class="composing">Send Notification</span><span class="sending">Sending&hellip;</span></button>
				</p>
				<div>
					<output class="w-100 pt-1" for="title message attachment" name="preview">
						<h2></h2>
						<div class="container-fluid p-0">
							<div class="row">
								<div class="col-9">
									<p class="m-0 p-0"></p>
								</div>
								<div class="col-3">
									<img class="mw-100" src="" />
								</div>
							</div>
						</div>
					</output>
				</div>
			</fieldset></form>
		</div>

		<footer class="my-3">
			<p class="text-body-secondary float-end">&copy; mdawaffe <?php echo esc_html( gmdate( 'Y' ) ); ?></p>
		</footer>
	</div>
	<script src="assets/js/bootstrap/bootstrap.bundle.min.js"></script>
	<script>
	(() => {
		const $feedback = document.getElementById( 'feedback' );
		const $form = document.querySelector( 'form' );
		const $fieldset = $form.querySelector( 'fieldset' );

		const $output = $form.preview;

		$feedback.addEventListener( 'click', () => {
			$feedback.classList.add( 'd-none' );
			$feedback.classList.remove( 'alert-danger', 'alert-success' );
			$feedback.replaceChildren();
		} );

		$form.addEventListener( 'keydown', event => {
			if ( event.keyCode !== 13 || ! event.metaKey ) {
				return;
			}

			if ( $form.reportValidity() ) {
				$form.dispatchEvent( new Event( 'submit' ) );
			}
		} );

		$form.addEventListener( 'input', event => {
			switch ( event.target ) {
				case $form.attachment:
					const $attachment = $output.querySelector( 'img' );
					if ( ! event.target.files.length ) {
						$attachment.src = '';
						break;
					}

					if ( event.target.files[0]?.size > 2097152 ) {
						event.target.classList.add( 'is-invalid' );
						event.target.setCustomValidity( 'Files must be smaller than ~2MB' );
						$attachment.src = '';
					} else {
						event.target.classList.remove( 'is-invalid' );
						event.target.setCustomValidity( '' );

						const url = URL.createObjectURL( attachment.files[0] );
						const revoker = () => {
							URL.revokeObjectURL( url );
							$attachment.removeEventListener( 'load', revoker );
						};
						$attachment.addEventListener( 'load', revoker );
						$attachment.src = url;
					}
					event.target.reportValidity();
					break;
				case $form.title:
					$output.querySelector( 'h2' ).textContent = event.target.value;
					break;
				case $form.message:
					$output.querySelector( 'p' ).textContent = event.target.value;
					break;
			}
		} );
		$form.addEventListener( 'submit', async event => {
			event.preventDefault();

			$feedback.classList.add( 'd-none' );

			const body = new FormData( $form );
			if ( ! $form.attachment.value ) {
				body.delete( 'attachment' );
			}

			$fieldset.disabled = true;

			const response = await fetch( $form.action, {
				method: $form.method,
				body,
			} );

			const result = await response.json();

			$fieldset.disabled = false;

			if ( response.ok ) {
				$feedback.classList.add( 'alert-success' );
				$feedback.classList.remove( 'alert-danger', 'd-none' );

				$feedback.textContent = 'Message sent.';
				$form.reset();

				const $outputCopy = $output.cloneNode( true );
				$feedback.append( ...$output.children );
				$output.append( ...$outputCopy.children );

				$output.querySelector( 'h2' ).textContent = '';
				$output.querySelector( 'p' ).textContent = '';
				$output.querySelector( 'img' ).src = '';
			} else {
				$feedback.classList.remove( 'alert-success', 'd-none' );
				$feedback.classList.add( 'alert-danger' );

				$feedback.textContent = `Error: ${ ( result?.errors ?? [] ).join( ', ' ) || 'Message not sent' }.`;
			}
		} );
	})();
	</script>
</body>
</html>
