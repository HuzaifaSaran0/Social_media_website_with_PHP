<?php
	$dsn = 'mysql: host=localhost; dbname=australia';
	$user = 'ozadmin';
	$password = '';

	try {
		$pdo = new PDO($dsn,$user,$password);
	}
	catch(PDOException $e) {
		die('oops');
	}

	//	MySQL
		$pdo -> exec('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
		$pdo -> exec('SET SESSION sql_mode = \'ANSI\';');

	//	Development
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//	True Prepared Statements
		$pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

	//	Extend PDOStatement
		$pdo -> setAttribute(PDO::ATTR_STATEMENT_CLASS, array('DBStatement',array($pdo)));

		class DBStatement extends PDOStatement {
			public $pdo;
			protected function __construct($pdo) {
				$this -> pdo = $pdo;
			}
			function interpolate(array $data = array()) {
				$string = $this -> queryString;
				$indexed = $data==array_values($data);
				foreach($data as $k => $v) {
					if(is_string($v)) $v = "'$v'";
					elseif($v === null) $v = 'NULL';
					if($indexed) $string = preg_replace('/\?/',$v,$string,1);
					else $string = str_replace(":$k",$v,$string);
				}
				return $string;
			}

			function fetchTemplate($template) {
				$rows = [];
				foreach($this as $row)
					$rows[] = preg_replace_callback('/{(.*?)}/',function($matches) use($row) {
						return $row[$matches[1]];
					},$template);
				return $rows;
			}
		}

	return $pdo;
