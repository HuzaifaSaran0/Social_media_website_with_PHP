<?php
	$title = $precis = $article = '';
	$id = 0;
	$errors = '';
	$disabled = '';
	$chooseImage = $previewImage = '';

	$pagetitle = 'Edit Blog';
	$pageheading = 'Edit Blog';
?>
<?php require_once 'includes/head.inc.php'; ?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="editblog">
			<form method="post" action="" enctype="multipart/form-data" id="editblog-form">
				<input type="hidden" name="id" value="<?= $id ?>">
				<?= $errors ?>
<!-- if not delete -->
				<fieldset id="new-image" <?= $disabled ?>>
					<div class="preview-image">
						<p>New Image (Shift-Click to Clear)</p>
						<input name="image" type="file" data-preview="preview-new-image">
					</div>
					<div id="use-image">
						<p>Choose Image</p>
						<div><?= $chooseImage ?></div>
					</div>
				</fieldset>
<!-- endif -->
				<fieldset id="content" <?= $disabled ?>>
					<?= $previewImage ?>
					<p><label>Title<br>
						<input type="text" name="title" value="<?= $title ?>"></label></p>
					<p><label>Precis<br>
						<textarea name="precis"><?= $precis ?></textarea></label></p>
					<p><label>Article<br>
						<textarea id="article" name="article"><?= $article ?></textarea></label></p>
				</fieldset>
				<p id="control">
					<a class="button" href="/admin-bloglist.php">Cancel</a>
<!-- if edit -->
					<button type="submit" name="update">Submit Changes</button>
<!-- elseif remove -->
					<button type="submit" name="delete">Delete this Article</button>
<!-- else -->
					<button type="submit" name="insert">Add Article</button>
<!-- endif -->
				</p>
			</form>
<!-- if not id -->
			<form id="editblog-import" method="post" enctype="multipart/form-data" action="">
					<label>Import Blog Articles</label> <input type="file" name="import-file" required>
					<button type="submit" name="import">Import</button>
			</form>
<!-- endif -->
		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
