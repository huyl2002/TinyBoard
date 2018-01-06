<?php

function getPdo()
{
	static $pdo = null; 
	if($pdo !== null)
		return $pdo;
	try
	{
		// echo 'pdo connecting <br>';
		$dsn    = sprintf('mysql:host=%s;dbname=%s;charset=utf8', DB_HOST, DB_NAME);
		$option = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
		return $pdo = new PDO($dsn, DB_USR, DB_PASSWD, $option);
	}
	catch (PDOException $e) { exit($e->getMessage()); }
}

function truncate(&$src, $max)
{
	$dst = 0;
	if(isset($src))
	{
		if($src >= $max)
			$dst = $max;
		else if($src < 0)
			$dst = 0;
		else 
			$dst = $src;
	}
	return $dst;
}
