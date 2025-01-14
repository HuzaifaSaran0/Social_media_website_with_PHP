<?php require_once 'includes/blog.code.php'; ?>
<?php require_once 'includes/head.inc.php'; ?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="blogarticle">
			<p id="precis"><?=$precis?></p>
			<p id="date"><?=$created?><?=$updated?></p>
			<div id="article">
				<?= $figure ?>
				<?= $article ?>
			</div>
			<a href="/bloglist.php" id="back" class="button">Back</a>
		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
