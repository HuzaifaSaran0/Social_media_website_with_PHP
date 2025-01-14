<?php #require_once 'includes/manage-images.code.php'; ?>
<?php
	//	Page Title and Heading
		$pagetitle = 'Upload Image';
		$pageheading = 'Upload Image';

	require_once 'includes/head.inc.php';
?>
<body>
<?php require_once 'includes/header.inc.php'; ?>
<?php require_once 'includes/nav.inc.php'; ?>
	<main>
		<article id="editimage">
			<form method="post" action="" enctype="multipart/form-data" id="editimage-form">
				<input type="hidden" name="id" value="[id]">
				[errors]
				<fieldset [disabled]>
<!-- if($id) get image:
					<p id="old-image">Current Image<br>
						<img id="preview" width="160" height="120" src="/images/thumbnails/<?= $src; ?>">
					</p>
endif; -->
<!-- if not prepare delete -->
					<p id="new-image" class="preview-image">
						<label>New Image (Shift-Click to Clear)<br>
							<input name="image" type="file" data-preview="preview-new-image">
						</label>
						<img id="preview-new-image">
					</p>
<!-- endif -->
					<p><label>Title<br>
						<input type="text" name="title" value="[title]"></label></p>
					<p><label>Description<br>
						<textarea name="description">[description]</textarea></label></p>

				</fieldset>
				<p id="control">
					<a class="button" href="/imagelist.php">Cancel</a>
<!--[if prepare update]
						<button type="submit" name="update">Submit Changes</button>
[elseif prepare delete]
						<button type="submit" name="delete">Delete Image</button>
[else]-->
						<button type="submit" name="insert">Add Image</button>
<!--[endif]-->
				</p>
			</form>
<!-- if(!$id) -->
			<form id="editimage-import" method="post" enctype="multipart/form-data">
					<label for="import-file">Import File</label>
					<input type="file" name="import-file" id="import-file" required>
					<button type="submit" name="import">Import</button>
			</form>
<!-- endif; -->
		</article>
	</main>
<?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>
