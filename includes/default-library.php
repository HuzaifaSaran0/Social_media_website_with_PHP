<?php
/*	resizeImage($source, $destination=null, $size='160x120', $options=[])
	================================================
	method:
		pad		pad with background
		clear	pad with transparency (png only)
		trim	trim shape
		scale	use $size as a percentage
	================================================ */

	function splitSize(string $size, $img=false) {
		$pattern = '/^\s*(\d+)\s*[xX]\s*(\d+)\s*$/';
		if(preg_match($pattern, $size, $matches))
			[, $width, $height] = $matches;
		else [$width, $height] = [null, null];

		if($img) {
			if($width && $height) return sprintf('width="%s" height="%s"', $width, $height);
			else return '';
		}
		else return [$width, $height];
	}

	function resizeImage(string $source, string $destination=null, string $size='160x120', array $options=[]) {
		$defaults = [
			'background' => [0,0,0],	//	r,g,b (black)
			'type' => null,				//	null: use same as source
			'method' => 'pad',			//	pad
		];
		$options = array_merge($defaults, $options);	//	aka:	$options = [...$defaults, ...$options];

		//	Set up
			if($options['method'] != 'scale') {
				$pattern = '/^\s*(\d+)\s*[xX]\s*(\d+)\s*$/';
				if(preg_match($pattern, $size, $matches)) {
					[, $width, $height] = $matches;
				}
				else {
					[$width, $height] = [160, 120];
				}
			}
			else $scale = floatval($size);


		//	Load Image
			[0=>$sw, 1=>$sh, 'mime'=>$mime] = getimagesize($source);
			switch ($mime) {
				case 'image/gif':
					$original = imagecreatefromgif($source);
					break;
				case 'image/jpeg':
					$original = imagecreatefromjpeg($source);
					break;
				case 'image/png':
					$original = imagecreatefrompng($source);
					break;
				default:
					$original = null;
			}

		//	Adjust Scale & Dimensions
			$sx = $sy = 0;
			//	$sw and $sh already defined
			$dx = $dy = 0;

			if($options['method'] != 'scale') {
				[$dw, $dh] = [$width, $height];

				if($sw/$sh < $dw/$dh) {		//	narrow
					$dw = $height * $sw / $sh;
					if($options['method'] != 'trim') $dx = ($width - $dw) / 2;
				}
				else {						//	wide
					$dh = $sh * $width / $sw;
					if($options['method'] != 'trim') $dy = ($height - $dh) / 2;
				}
			}
			else {
				[$dw, $dh] = [$sw * $scale / 100, $sh * $scale / 100];
			}

		//	Create Blank Image
			switch($options['method']) {
				case 'trim':
				case 'scale':
					$copy = imagecreatetruecolor((int) $dw, (int) $dh);
					break;
				case 'pad':
				case 'clear':
				default:
					$copy = imagecreatetruecolor($width, $height);
			}

		//	Background Colour
			switch($options['method']) {
				case 'pad':
					$background = imagecolorallocate($copy, ...$options['background']);
					imagefill($copy, 0, 0, $background);
					break;
				case 'clear':
					$background = imagecolorallocatealpha($copy, 0, 0, 0, 127);
					imagefill($copy, 0, 0, $background);
					imagesavealpha($copy, true);
			}

		//	Copy Original into New Image
			imagecopyresampled(
				$copy, $original,				 			// destination image, source image
				(int) $dx, (int) $dy, (int) $sx, (int) $sy,	// left-top corner (origin)
				(int) $dw, (int) $dh, (int) $sw, (int) $sh	// width, height
			);

		//	Save New Image
			//	Missing Destination
				if(!$destination) {
					['dirname'=>$dirname, 'basename'=>$basename, 'extension'=>$extension, 'filename'=>$filename] = pathinfo($source);
					$destination = "$dirname/$filename-{$width}x{$height}.$extension";
				}
			//	Check Mime Type
				['dirname'=>$dirname, 'basename'=>$basename, 'extension'=>$extension, 'filename'=>$filename] = pathinfo($destination);
				$imageTypes = ['gif'=>'image/gif','jpeg'=>'image/jpeg','jpg'=>'image/jpeg','png'=>'image/png'];
				if($options['type']) $extension = $options['type'];
				if($options['method']=='clear') $extension = 'png';

				$destination = "$dirname/$filename.$extension";

			$mime = $imageTypes[$extension];
			switch ($mime) {
				case 'image/gif':
					imagegif($copy, $destination);
					break;
				case 'image/jpeg':
					imagejpeg($copy, $destination);
					break;
				case 'image/png':
					imagepng($copy, $destination);
					break;
				default:

			}

	}
/*
	resizeImage(
		'/Users/mark/Library/Mobile Documents/com~apple~CloudDocs/Writing/Apress/PHP101/images/1665_Girl_with_a_Pearl_Earring.jpg',
		null,
		'1280 x 960',
		[
			'background' => [65,162,222],	//	r,g,b
			'type' => 'png',
			'method' => 'clear'
		]
	);
	resizeImage(
		'/Users/mark/Library/Mobile Documents/com~apple~CloudDocs/Writing/Apress/PHP101/images/DSC06033.JPG',
		'/Users/mark/Library/Mobile Documents/com~apple~CloudDocs/Writing/Apress/PHP101/images/DSC06033.png',
		'320 x 240'
	);
*/

/*	unzip($source, $destination)
	================================================
	returns ['files' => $files, 'names' => $names]
	================================================ */

	function unzip(string $source, string $destination): array {
		$zip = new ZipArchive;
		$zip -> open($source);

		#	$zip -> extractTo($destination);

		$files = []; $names = [];
		for($i = 0; $i < $zip->numFiles; $i++) {
			$file = $zip -> getNameIndex($i);
			if($file[-1] == '/') continue;
			$name = basename($file);
			copy("zip://$source#$file", "$destination/$name");
			$files[] = $file;
			$names[] = $name;
		}
		$zip -> close();

		return ['files' => $files, 'names' => $names];
	}

/*	MimeType($filename)
	================================================
	returns Mime Type of File
	================================================ */

	function MimeType($filename) {
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		return $finfo->file($filename);
	}

/*	clearDir($dir)
	================================================
	Simple wrapper function to clear a directory.
	================================================ */

	function clearDir(string $dir) {
		array_map('unlink', array_filter(glob("$dir/*")));
	}

/*	printr($data)
	================================================
	Wraps print_r() inside a <pre> block.
	================================================ */

	function printr($data) {
		print '<pre>';
		print_r($data);
		print '</pre>';
	}

/*	md2html($text)
	================================================
	Very simple markdown parser.
	Heavily inspired by:
		https://github.com/jbroadway/slimdown
	================================================ */

	function md2html($text) {
		//	Headings
			$text = preg_replace_callback('/^(#{3,})(.*)/m', function($data) {
				[, $level, $text] = $data;
				return sprintf('<h%1$s>%2$s</h%1$s>', strlen($level), trim($text));
			}, $text);

		//	Anchors
			$text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" target="_blank">$1</a>', $text);

		//	Strong & Emphasis
			$text = preg_replace('/(\*\*|__)(.*?)\1/', '<strong>$2</strong>', $text);
			$text = preg_replace('/(\*|_)(.*?)\1/', '<em>$2</em>', $text);
			$text = preg_replace_callback('/<a href="(.*?)"/', function($data) {
				$href = preg_replace('/<\/?em>/', '_', $data[1]);
				return sprintf('<a href="%s"', $href);
			}, $text);

		//	Lists
			$text = preg_replace_callback('/\n(\*|-)\s*(.*)/', function($data) {
				[, , $text] = $data;
				return sprintf("\n<ul>\n\t<li>%s</li>\n</ul>", trim($text));
			}, $text);
			$text = preg_replace('/<\/ul>\n<ul>\n/', '', $text);

			$text = preg_replace_callback('/\n\d+\.\s*(.*)/', function($data) {
				[, $text] = $data;
				return sprintf("\n<ol>\n\t<li>%s</li>\n</ol>", trim($text));
			}, $text);
			$text = preg_replace('/<\/ol>\n<ol>\n/', '', $text);

		//	Block Quotes
			$text = preg_replace_callback('/\n\>\s*(.*)/', function($data) {
				[, $text] = $data;
				return sprintf("\n<blockquote>%s</blockquote>", trim($text));
			}, $text);
			$text = preg_replace('/<\/blockquote>\n<blockquote>/', ' ', $text);

		//	Line Breaks
			$text = preg_replace('/ {2,}\n/', "<br>\n", $text);
		//	Tables
			$text = preg_replace_callback(
				'((((\|[^\|\n]*)+)\n)(((\|-{3,})+)\|?\n)(((\|[^\|\n]*)+)\n)+)', function($data) {
					$data = explode("\n", trim($data[0]));

					$table = [];
					$table[] = '<table>';
					array_splice($data,1,1);
					$row = array_shift($data);
					$row = trim($row,'|');
					$row = explode('|',$row);
					$row = array_map('trim',$row);
					$row = sprintf("\t<thead>\n\t\t<tr><th>%s</th></tr>\n\t</thead>",implode('</th><th>',$row));
					$table[] = $row;

					$table[] = "\t<tbody>";

					foreach($data as $key => &$row) {
						$row = trim($row,'|');
						$row = explode('|',trim($row));
						$row = array_map('trim',$row);
						$row = sprintf("\t\t<tr><td>%s</td></tr>",implode('</td><td>',$row));
						$table[] = $row;
					}

					$table[] = "\t</tbody>";

					$table[] = "</table>\n";

					return implode("\n",$table);
				},
				$text
			);

		//	Paragraphs
			$text = preg_replace_callback('/\n{2,}([\S\s]*?)(?=\n{2,}|$)/', function($data) {
					[, $text] = $data;
					$trimmed = trim($text);
					if (preg_match ('/<\/?(ul|ol|li|h|p|bl|table)/m', $trimmed)) return "$text";
					return sprintf ("\n\n<p>%s</p>\n\n", $trimmed);
			}, $text);

		return $text;
	}
