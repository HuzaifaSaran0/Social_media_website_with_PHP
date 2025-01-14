<?php
	//	Page Title and Heading
		$pagetitle = 'Contact';
		$pageheading = 'Contact Us';

	require_once 'includes/head.inc.php';
?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="contact">
<!-- if not sent: -->
			<form id="contact-form" method="post" action="" novalidate>
				[errors]
				<p><label for="name">Name:</label>
					<input name="name" id="name" type="text" value="[name]">
				</p>
				<p><label for="email">Email:</label>
					<input name="email" id="email" type="text" value="[email]">
				</p>
				<p><label for="subject">Subject:</label>
					<input name="subject" id="subject" type="text" value="[subject]">
				</p>
				<p><label for="message">Message:</label>
					<textarea name="message" id="message">[message]</textarea>
				<p><button type="submit" name="send">Send Message</button></p>
			</form>
<!-- else:
			<p>Thank you for your message.</p>
			<p>Now, <a href="/">go away …</a>.</p>
endif -->
		</article>
<?php require_once 'includes/aside.inc.php'; ?>

	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
