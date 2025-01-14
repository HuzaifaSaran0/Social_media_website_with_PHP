<?php
	require_once 'includes/db.php';

	$tbody = $paging = '';
?>
<?php
	$pagetitle = 'Image List';
	$pageheading = 'Image List';

	require_once 'includes/head.inc.php';
?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="imagelist">
			<form method="post" action="editimage.php" id="editblog" class="manage-list">
				<input type="hidden" name="page" value="<?= $page; ?>">
				<table class="manage">
					<thead>
						<tr><th>ID</th><th>&nbsp;</th><th>Title</th><th>&nbsp;</th><th>&nbsp;</th></tr>
					</thead>
					<tbody>
						<?= $tbody; ?>
					</tbody>
				</table>
			</form>
			<p id="paging"><?= $paging; ?></p>
			<form method="post" action="editimage.php">
				<p id="control"><button type="submit" name="prepare-insert">Add New Image</button></p>
<!--
				<p><button type="submit" name="export">Export Image CSV</button></p>
-->
			</form>
		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
