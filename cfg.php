<?php 
// create time: 2018-01-05 02:24:13

define('DB_HOST', 'localhost'); 
define('DB_USR', 'root'); 
define('DB_PASSWD', '123'); 
define('DB_NAME', 'board'); 
define('LINE_PER_PAGE', 10); 
define('PAGE_PER_CHAPTER', 5); 
define('TINY_DEBUG', 1); 

if(TINY_DEBUG == true)
{
	error_reporting(E_ALL);
	ini_set('display_errors','On');
}
else
{
	error_reporting(E_USER_NOTICE);
	ini_set('display_errors','Off');
	ini_set('log_errors', 'On');
}

