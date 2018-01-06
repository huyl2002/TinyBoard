<?php
// TinyBoard 安装程序
?>
<!DOCTYPE html>
<html>
<head> <meta charset="UTF-8"><title>TinyBoard | Install </title></head>
<body>

<?php
if(version_compare('7.0.0', PHP_VERSION, '>='))
	die('Current PHP version:' . PHP_VERSION . '. too low, requiring version higher than 7.0.' . "\n");

if(empty($_POST) || !isset($_POST['host']))
{
	?>
	<form method = "post">
	     <h3>database</h3> 
		  host <input type="text" name="host" value="localhost"><br />
		  user <input type="text" name="user" value="root"><br />
		  password <input type="text" name="password" value="123"><br />
		  dbname <input type="text" name="dbname" value="board"><br />
		  <input type="submit" value="install">
	</form>
	<?php 
	goto End;
}

// step 1 显示输入内容
echo '<h3>Input</h3><br />';
echo 'dbhost ', $_POST['host'], '<br />';
echo 'dbname ', $_POST['dbname'], '<br />';
echo 'user ', $_POST['user'], '<br />';
echo 'password  ', $_POST['password'], '<br />';
echo 'installing ... ', '<br />';

// step 2 安装数据库
$addUser = <<<QUERY
	drop table if exists user;
	create table user
	(
		id int not null auto_increment,
		name varchar(30) not null,
		passwd varchar(30) not null,
		phone varchar(30) not null,
		primary key(id)
	);
	insert into user values(1, 'Huyelei', '123', '18262289511');
    insert into user values(2, 'root',   '123', '133');
	insert into user values(3, 'LiLei',   '789', '139');
	insert into user values(4, 'HanMeimei', 'ABC', '188');
QUERY;

$addMessage = <<<QUERY
	drop table if exists message;
	create table message
	(
		id int not null auto_increment,
		content varchar(255) not null,
		author int not null,
		authorName varchar(30) not null,
		date DATETIME not null,
		primary key(id)
	);
	create index index_msg_author on message(author);
QUERY;

$host = $_POST['host'] ?? '';
$dbname = $_POST['dbname'] ?? '';
$dbusr = $_POST['user'] ?? '';
$passwd = $_POST['password'] ?? '';

try {
	$dsn = sprintf('mysql:host=%s;dbname=%s', $host, $dbname);
	$option = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
	$pdo = new PDO($dsn, $dbusr, $passwd, $option);
} catch (PDOException $e) { exit($e->getMessage()); }

$pdo->query($addUser);
$pdo->query($addMessage);

$tables = $pdo->query('show tables')->fetchAll(PDO::FETCH_GROUP);
if(!in_array('user', array_keys($tables)) || !in_array('message', array_keys($tables)))
	die('add table fail');

// 循环输入message table内容
$stm = $pdo->prepare('insert into message values(?, ?, ?, ?, ?)');
$stm->bindParam(1, $id);
$stm->bindParam(2, $msg);
$stm->bindParam(3, $author);
$stm->bindParam(4, $authorName);
$stm->bindParam(5, $time);
$names = [1=> 'Huyelei', 2=>'root', 3=>'LiLei', 4=>'HanMeimei'];
for($i=1; $i<=300; $i++) // 300
{
	$id = $i;
	$msg = sprintf('this is number %s message', $i);
	$author = ($i-1)%3 + 1;
	$authorName = $names[$author];
	$time = sprintf('2018-01-03 15:00::%02d', $i%60);
	$stm->execute();
}
$stm->rowCount() > 0 or die('set msg content fail');
	
// step 3 生成配置文件
$f = fopen('cfg.php', 'w') or die(__FILE__ . ' line ' . __LINE__);
fwrite($f, "<?php \n"); 
fwrite($f, '// create time: '. date('Y-m-d H:i:s') . "\n\n");
function writeConst($f, $dst, $src)
{
	$str = sprintf("define('%s', '%s'); \n", $dst, $src);
	fwrite($f, $str);
}
writeConst($f, 'DB_HOST',  $host);
writeConst($f, 'DB_USR',   $dbusr);
writeConst($f, 'DB_PASSWD', $passwd);
writeConst($f, 'DB_NAME',  $dbname);

function writeConst_($f, $dst, $src)
{
	$str = sprintf("define('%s', %s); \n", $dst, $src);
	fwrite($f, $str);
}
writeConst_($f, 'LINE_PER_PAGE',  10);    
writeConst_($f, 'PAGE_PER_CHAPTER', 5);  
writeConst_($f, 'TINY_DEBUG', true);

fwrite($f, "\n");
$str = <<<CODE
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
CODE;
fwrite($f, $str);
fwrite($f, "\n\n");
fclose($f);

echo 'installation successfully finished. <br />';

End: 
?>
</body></html>

