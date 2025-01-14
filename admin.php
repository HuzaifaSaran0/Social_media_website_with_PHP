<?php
	$pagetitle = 'Administration';
	$pageheading = 'Administration';

	require_once 'includes/head.inc.php';
?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="admin">
<!-- if not logged in -->
			<form id="login" method="post" action="">
				<?=$errors?>
				<p><label>Email Address:<br><input type="text" name="email"></label></p>
				<p><label>Password:<br>
					<input type="password" name="password" id="password"><button type="button" name="show" title="Show Password">👀</button></label>
				</p>
				<p><button type="submit" name="login">Login</button></p>
			</form>
<!-- else -->
			<ul id="admin-links">
				<li><a href="imagelist.php">Manage Images</a></li>
				<li><a href="admin-bloglist.php">Manage Blog</a></li>
			</ul>
			<form method="post" action="">
				<button type="submit" name="logout">Logout</button>
			</form>
<!-- endif -->
		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
