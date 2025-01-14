<?php
	//	Environment
		$root = $_SERVER['WEB_ROOT'] = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']);
		$host = $_SERVER['HTTP_HOST'];
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';

		$date = date('l, jS F Y g:i a');
		$timezone = date_default_timezone_get();

	//	Library
		$CONFIG = parse_ini_file("$root/setup/config.ini.php", true);
		require_once("$root/includes/default-library.php");

	//	Action
		$action = @$_GET['action'];
		$message = '';

	//	Functions
		function nl2pilcrow($text) {
			return str_replace(["\r\n", "\r", "\n"], '¶', $text);
		}

		function sql2CSV($sql, $headers, $filename) {
			global $pdo;

			$data = $pdo -> query($sql) -> fetchAll(PDO::FETCH_NUM);
			array_unshift($data, $headers);

			header("Content-type: text/csv; charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");

			$csv = fopen('php://output', 'w');
			foreach($data as $row) fputcsv($csv, $row);
			fclose($csv);
		}

		function addImageData(string $name, string $title, string $description, $gallery=1) {
			global $pdo;

			$name = strtolower($name);
			$name = str_replace(' ', '-', $name);

		//	Add to the Database
			$description = nl2pilcrow($description);

			$sql = 'INSERT INTO images(title, description, name, src, gallery) VALUES(?, ?, ?, ?, ?)';
			$pdoStatement = $pdo -> prepare($sql);
			$data = [$title, $description, $name, $name, $gallery];
			$pdoStatement -> execute($data);

			$id = $pdo -> lastInsertId();
			$src = sprintf('%06s-%s', $id, $name);

			$sql = 'UPDATE images SET src=? WHERE id=?';
			$pdoStatement = $pdo -> prepare($sql);
			$data = [$src, $id];
			$pdoStatement -> execute($data);

			return [$id, $src];
		}

		function addImageFile(string $file, string $src) {
			global $root, $CONFIG;
			copy($file, "{$root}/{$CONFIG['images']['directory']}/originals/{$src}");

			resizeImage(
				"{$root}/{$CONFIG['images']['directory']}/originals/{$src}",
				"{$root}/{$CONFIG['images']['directory']}/display/{$src}",
				$CONFIG['images']['display-size']
			);
			resizeImage(
				"{$root}/{$CONFIG['images']['directory']}/originals/{$src}",
				"{$root}/{$CONFIG['images']['directory']}/thumbnails/{$src}",
				$CONFIG['images']['thumbnail-size']
			);
			resizeImage(
				"{$root}/{$CONFIG['images']['directory']}/originals/{$src}",
				"{$root}/{$CONFIG['images']['directory']}/icons/{$src}",
				$CONFIG['images']['icon-size']
			);
			resizeImage(
				"{$root}/{$CONFIG['images']['directory']}/originals/{$src}",
				"{$root}/{$CONFIG['images']['directory']}/scaled/{$src}",
				$CONFIG['images']['scaled'], ['method' => 'scale']
			);
		}

		//  Add Blog Data
   		function addBlogData(
			string $title, string $precis,
	   		string $article, array|string $image=null, string $created=null,
	       	string $updated=null, $markdown=false) {

   			global $pdo, $root, $CONFIG;

   			$precis = nl2pilcrow($precis);
   			$article = nl2pilcrow($article);

   			$sql = $markdown
				?	'INSERT INTO blog(title, precis, article, created, updated, markdown)
   		        	VALUES(?, ?, ?, ?, ?, ?)'
				:	'INSERT INTO blog(title, precis, article, created, updated)
   		        	VALUES(?, ?, ?, ?, ?)';
   		    $pdoStatement = $pdo -> prepare($sql);
   		    $data = [$title, $precis, $article, $created?:date('Y-m-d H:i:s'), $updated?:date('Y-m-d H:i:s')];
			if($markdown) $data[] = $markdown;

   		    $pdoStatement -> execute($data);

   			$id = $pdo -> lastInsertId();

   			if($image) addBlogImage($id, $image, $title, $precis);

   			return $id;
   		}

       //  Add Blog Image
           function addBlogImage(int $id, array|string $image,
             string $title, string $precis) {
               global $pdo, $root, $CONFIG;

   			if(is_array($image)) {
   				['imageName' => $name, 'imageFile' => $file] = $image;

   				$name = strtolower($name);
   			    $name = str_replace(' ', '-', $name);

   			    $sql = 'INSERT INTO images(title, description, name, src, gallery)
   			        VALUES(?, ?, ?, ?, false)';
   			    $pdoStatement = $pdo -> prepare($sql);
   			    $data = [$title, $precis, $name, $name];
   			    $pdoStatement -> execute($data);

   			    $imageid = $pdo -> lastInsertId();
   			    $src = sprintf('%06s-%s', $imageid, $name);

   			    $sql = 'UPDATE images SET src=? WHERE id=?';
   			    $pdoStatement = $pdo -> prepare($sql);
   			    $pdoStatement -> execute([$src, $imageid]);

   				$sql = 'UPDATE blog SET imageid=? WHERE id=?';
   			    $pdoStatement = $pdo -> prepare($sql);
   			    $pdoStatement -> execute([$imageid, $id]);

   				copy($file,
   		            "$root/{$CONFIG['images']['directory']}/originals/$src");

   		        resizeImage(
   		            "$root/{$CONFIG['images']['directory']}/originals/$src",
   		            "$root/{$CONFIG['images']['directory']}/display/$src",
   		            $CONFIG['images']['display-size']
   		        );
   		        resizeImage(
   		            "$root/{$CONFIG['images']['directory']}/originals/$src",
   		            "$root/{$CONFIG['images']['directory']}/thumbnails/$src",
   		            $CONFIG['images']['thumbnail-size']
   		        );
   		        resizeImage(
   		            "$root/{$CONFIG['images']['directory']}/originals/$src",
   		            "$root/{$CONFIG['images']['directory']}/icons/$src",
   		            $CONFIG['images']['icon-size']
   		        );
   		        resizeImage(
   		            "$root/{$CONFIG['images']['directory']}/originals/$src",
   		            "$root/{$CONFIG['images']['directory']}/scaled/$src",
   		            $CONFIG['images']['scaled'], ['method' => 'scale']
   		        );
   	    	}
   			elseif(is_numeric($image)) {
   				$sql = 'UPDATE blog
   	                SET imageid=(select id from images where id=?)
   	                WHERE id=?';
   	            $pdoStatement = $pdo -> prepare($sql);
   	            $pdoStatement -> execute([$image, $id]);
   	        }

               return $id;
           }

	if($action) {
		//	PDO

			define('USER', 'root');
			define('PASSWORD', '');
			$dsn = 'mysql:host=localhost;charset=utf8mb4';

			try {
				$pdo = new PDO($dsn, USER, PASSWORD);
			} catch(PDOException $e) {
				die ($e -> getMessage());		//	Exit, displaying the error message
			}

			if($action != 'australia') $pdo -> exec('USE australia;');

			$pdo -> exec('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
			$pdo -> exec('SET SESSION sql_mode = \'ANSI\';');

			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//	Action
			switch($action) {
				case 'australia':
					$pdo -> exec(file_get_contents("$root/setup/mysql/australia.sql"));
					$message = 'CREATE DATABASE australa<br>CREATE USER ozadmin';
					break;

				case 'imagescreate':
					$pdo -> exec(file_get_contents("$root/setup/mysql/imagescreate.sql"));
					$message = 'CREATE TABLE images';
					break;

				case 'reset-images':
					$sql = 'TRUNCATE TABLE images';
					$pdo -> exec($sql);

					$directories = ['uploads', 'originals', 'display', 'thumbnails', 'icons', 'scaled'];
					foreach($directories as $dir) clearDir("$root/images/$dir");

					$message = 'TRUNCATE TABLE images';
					break;

				case 'upload-images':
					$pdo -> exec('TRUNCATE TABLE images');
					$directories = ['uploads', 'images/originals', 'images/display', 'images/thumbnails', 'images/icons', 'images/scaled'];
					foreach($directories as $dir) array_map('unlink', glob("$root/$dir/*"));

					['files' => $files, 'names' => $names] = unzip("$root/setup/animals.zip", "$root/uploads");

					$file = "$root/uploads/@index.csv";
					$data = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
					$header = array_shift($data);
					$data = array_map('str_getcsv', $data);

					$imagetypes = ['image/gif', 'image/jpeg', 'image/png', 'image/webp'];

					foreach($data as $item) {
						if(
							!file_exists("$root/uploads/$item[0]")
							|| !in_array(MimeType("$root/uploads/$item[0]"), $imagetypes)
						) continue;
						[, $src] = addImageData($item[0], $item[1], $item[2]);
						addImageFile("$root/uploads/$item[0]", $src);
					}

					array_map('unlink', glob("$root/uploads/*"));
					array_map('unlink', glob("$root/uploads/._*"));

					$message = 'CREATE TABLE images';
					break;

				case 'users':
					$pdo -> exec(file_get_contents("$root/setup/mysql/users.sql"));
					$message = 'CREATE TABLE users';
					break;

				case 'user':
					$sql = 'INSERT INTO users(email, familyname, givenname, hash, admin) VALUES (?, ?, ?, ?, ?)';
					$email = $_GET['email'];
					$password = $_GET['password'];
					$password = password_hash($password, PASSWORD_DEFAULT);
					$data = [$email, $_GET['familyname'], $_GET['givenname'], $password, 1];
					$pdo -> prepare($sql) -> execute($data);

					$message = 'INSERT INTO users(email, familyname, givenname, hash, admin) VALUES (?, ?, ?, ?, ?)';
					break;

				case 'blogcreate':
					$pdo -> exec(file_get_contents("$root/setup/mysql/blogcreate.sql"));
					$message = 'CREATE TABLE blog';
					break;

				case 'reset-blog':
					$pdo -> exec('TRUNCATE TABLE blog');
					break;

				case 'blog-markdown':
				case 'import-blog':
					['files' => $files, 'names' => $names] = unzip("$root/setup/blog.zip", "$root/uploads");

					if($action == 'blog-markdown') {
						$pdo -> exec('ALTER TABLE blog ADD COLUMN markdown BOOLEAN DEFAULT FALSE');
						$file = "$root/uploads/@markdown.csv";
						$keys = explode(' ', 'title precis article created updated image markdown');
						$message = 'Blog with Markdown';
					}
					else {
						$pdo -> exec('DELETE FROM images WHERE NOT gallery');
						$file = "$root/uploads/@blog.csv";
						$keys = explode(' ', 'title precis article created updated image');
						$message = 'Import Blog';
					}

					$data = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
					$header = array_shift($data);
					$data = array_map('str_getcsv', $data);

					$imagetypes = ['image/gif', 'image/jpeg', 'image/png', 'image/webp'];


					foreach($data as $row) {
						$row = array_combine($keys, $row);
						$image = null;

						//  Check for an image
							if($row['image'] && file_exists("$root/uploads/{$row['image']}")) $row['image'] = [
								'imageName' => $row['image'],
								'imageFile' => "$root/uploads/{$row['image']}"
							];
							else $row['image'] = null;

						//  addBlogData()
							$row['markdown'] = $action == 'blog-markdown';
							addBlogData(...$row);   							//  PHP >= 8
							//  addBlogData(...array_values($row), $action == 'blog-markdown');             //  PHP >= 7.2
							//  addBlogData($row['title'], $row['precis'], $row[article],
							//    $row[created], $row[updated], $row[image], $action == 'blog-markdown');   //  Any  PHP
					}

					array_map('unlink', glob("$root/uploads/*"));
					array_map('unlink', glob("$root/uploads/._*"));
					break;

				case 'export-images':
					$sql = 'SELECT name, title, description FROM images';
					$data = $pdo -> query($sql) -> fetchAll(PDO::FETCH_NUM);
					foreach($data as $key => &$row) $row[2] = preg_replace('/\n+/', '\n', $row[2]);
					array_unshift($data, ['name', 'title', 'description']);
					header("Content-type: text/csv; charset=utf-8");
					header("Content-Disposition: attachment; filename=export-images.csv");
					$csv = fopen('php://output', 'w');
					foreach($data as $row) fputcsv($csv, $row);
					fclose($csv);

					$message = 'Export Images';
					break;

				case 'export-blog':
					$sql = 'SELECT b.title, b.precis, b.article, b.created, b.updated, i.name, b.markdown
						FROM blog AS b LEFT JOIN images AS i ON b.imageid=i.id';
					$headers = ['title', 'precis', 'article', 'created', 'updated', 'image', 'markdown'];
					sql2CSV($sql, $headers, '@blog.csv');

					$message = 'Export Blog (with Markdown)';
					break;

				default:
		}

		header("Location: $protocol://$host{$_SERVER['SCRIPT_NAME']}");
		exit;

	}
?>
<?php
	$pagetitle = 'Database Setup';
	$pageheading = 'Database Setup';
#	require_once 'includes/head.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="/favicon.png">
	<title>Australia Down Under<?php if(@$pagetitle) print " — $pagetitle";?></title>
	<link rel="stylesheet" type="text/css" href="/styles/styles.css">
	<link rel="stylesheet" type="text/css" href="/setup/setup.css">
	<script type="text/javascript" src="/scripts/script.js" crossorigin="anonymous" defer></script>
	<script type="text/javascript" src="/setup/setup.js" crossorigin="anonymous" defer></script>
</head>
<body>
<?php require_once "$root/includes/header.inc.php"; ?>
<?php require_once "$root/includes/nav.inc.php"; ?>
	<main>
		<article id="setup">
			<p id="message"><?= $message ?></p>
			<form id="setup" method="get" action="">
			<ul id="setup-items">
				<li><span>Database</span>
					<ul>
						<li><button type="submit" name="action" value="australia">Create Database</button></li>
					</ul>
				</li>
				<li><span>Images</span>
					<ul>
						<li><button type="submit" name="action" value="imagescreate">Create Images Table</button></li>
						<li><button type="submit" name="action" value="reset-images">Reset Images</button></li>
						<li><button type="submit" name="action" value="upload-images">Upload Images</button></li>
					</ul>
				</li>
				<li><span>Users</span>
					<ul>
						<li><button type="submit" name="action" value="users">Create Users</button></li>
						<li><button type="submit" name="action" value="user">Create Admin User</button>
							<div>
								<label><span>Given Name: </span><input type="text" name="givenname"></label>
								<label><span>Family Name: </span><input type="text" name="familyname"></label>
								<label><span>Email: </span><input type="email" name="email"></label>
								<label><span>Password: </span><input type="text" name="password"></label>
							</div>
						</li>
						<li><button type="submit" name="do-password-hash">PHP Password</button>
							<div>
								<label><span>Password: </span><input type="text" name="password-text"></label>
								<label><input type="text" name="password-hash"></label>
							</div>
						</li>
					</ul>
				</li>
				<li><span>Blog</span>
					<ul>
						<li><button type="submit" name="action" value="blogcreate">Create Blog Table</button></li>
						<li><button type="submit" name="action" value="reset-blog">Reset Blog</button></li>
						<li><button type="submit" name="action" value="import-blog">Import Blog</button></li>
						<li><button type="submit" name="action" value="blog-markdown">Blog with Markdown</button></li>
					</ul>
				</li>
				<li><span>Export to CSV</span>
					<ul>
						<li><button type="submit" name="action" value="export-images">Export Images</button></li>
						<li><button type="submit" name="action" value="export-blog">Export Blog (includes Mardkown)</button></li>
					</ul>
				</li>
			</ul>
			</form>
		</article>
	</main>
<?php require_once "$root/includes/footer.inc.php"; ?>
</body>
