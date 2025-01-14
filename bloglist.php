<?php
	$articles = $paging = $displaying = '';
?>
<?php
	$pagetitle='Australia Down Under — Blog';
	$pagesubtitle='Blog Articles';
	require_once 'includes/head.inc.php';
?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="bloglist">
			<?= $articles; ?>
			<p id="paging"><?= $paging; ?></p>
			<p id="displaying"><?= $displaying; ?></p>
		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
