<?php
	$table = '';
?>
<?php
	$pagetitle = 'Config';
	$pageheading = 'Configuration';

	require_once 'includes/head.inc.php';
?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="config">
			<form id="config-form" method="post" action="">
				<table>
					<?= $table ?>
				</table>
				<p><label><input type="checkbox" name="resize"> Resize Images</label></p>
				<p id="control"><button type="submit" name="save">Save</button></p>
			</form>

		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
