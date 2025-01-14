<?php
	/*	Ajax Services
		================================================ */

		$_REQUEST=array_merge($_GET,$_POST);

		//	Password Hash

			if(isset($_REQUEST['password'])) {
				$password = trim($_REQUEST['password']);
				$hash = password_hash($password,PASSWORD_DEFAULT);
				header('Content-Type: text/plain');
				print $hash;
				exit;
			}
?>
