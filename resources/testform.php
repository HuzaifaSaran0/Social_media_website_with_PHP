<?php
	function entabulate($array, $id, $caption = NULL) {
		$table = [];
		$table[] = sprintf('<table id="%s"><tbody>',$id);
		foreach ($array as $k =>$v) {
			if(is_array($v)) $v = sprintf('Array: %s', implode(';', $v));
			$table[] = "<tr><th>$k</th><td>$v</td></tr>";
		}
		$table[] = "</tbody></table>\n";
		return implode("\n",$table);
	}

	function printarray($array) {
		$array = print_r($array, true);
		$array = explode("\n",$array);
		$array = array_slice($array,2,-2);
		$array = array_map('trim',$array);
		return implode("\n",$array);
	}

	$accept = @$_SERVER['HTTP_ACCEPT'];
	[$cookiestable, $gettable, $posttable] =
		[entabulate($_COOKIE, 'cookies-table', 'Cookies'), entabulate($_GET, 'get-table', 'GET Data'), entabulate($_POST, 'post-table', 'POST Data')];
	$rawget = printarray($_GET);
	$rawpost = printarray($_POST);
?>
<?php
		$pagetitle = 'Australia Down Under';
		$pagesubtitle = 'Test Form';
?>
<?php require_once '../includes/head.inc.php'; ?>
<body>
	<link rel="stylesheet" href="/assets/testform.css">
<?php require_once '../includes/header.inc.php'; ?>
<?php require_once '../includes/nav.inc.php'; ?>
	<main>
		<article id="testform">
			<h2>Accept:</h2>
			<p id="accept-string"><?=$accept?></p>
			<h2>Cookies</h2>
			<?=$cookiestable?>
			<h2>Get Data</h2>
			<?=$gettable?>
			<h2>Post Data</h2>
			<?=$posttable?>
			<h2>Raw Data</h2>
			<table id="raw">
				<tbody>
				<tr id="rawget">
					<th>Get</th>
					<td><?=$rawget?></td>
				</tr>
				<tr id="rawpost">
					<th>Post</th>
					<td><?=$rawpost?></td>
				</tr>
			</tbody>
			</table>
		</article>
	</main>
<?php require_once '../includes/footer.inc.php'; ?>
</body>
</html>
