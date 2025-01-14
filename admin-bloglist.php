<?php
	$tbody = $paging = $displaying = '';
?>
<?php
	$pagetitle = 'Administration Blog List';
	$pageheading = 'Blog List';

	require_once 'includes/head.inc.php';
?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="admin-bloglist">
			<form method="post" action="editblog.php" id="editblog" class="manage-list">
				<p><input type="hidden" name="page" value=""></p>
				<table class="manage">
					<thead>
						<tr><th>ID</td><th>Title</th><th>Created</th><th>Updated</th><th>&nbsp;</th><th>&nbsp;</th></tr>
					</thead>
					<tbody>
						<?= $tbody; ?>
					</tbody>
				</table>
				<p id="control"><button type="submit" name="prepare-insert">Add New Article</button></p>
			</form>
			<p id="paging"><?= $paging; ?></p>
			<p id="displaying"><?= $displaying; ?></p>
		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
