<?php
	$pagetitle = 'Gallery';
	$pageheading = 'Gallery';

	require_once 'includes/head.inc.php';
?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="gallery">

			<div id="thumbnails">
				<div><?= $images; ?></div>
				<p id="paging"><?= $paging; ?></p>
				<p id="displaying"><?= $displaying; ?></p>
			</div>
			<div id="mainimage">
				<h2><?= $title; ?></h2>
				<p><?= $image; ?></p>
				<div class="caption"><?= $description; ?></div>
			</div>
		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
