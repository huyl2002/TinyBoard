<?php
// 对某个消息的增删改页面, 增删改类型依次为 1 2 3
?>
<!DOCTYPE html>
<html><head>
<meta charset="UTF-8">
<title>TinyBoard | manage </title>
</head> <body>
<a href="index.php">首页</a><br /><br />
<?php 
session_start();
require 'cfg.php';
require 'util.php';
$type = truncate($_GET['type'], 4);
$type > 0 && $type < 4 or die('die at line:' . __LINE__);

if($type == 1) // 增
{
	if(!isset($_GET['ready'])) // 未上传参数
	{
		?>
		<form>
		  content <input type="text" name="content"><br />
		  <input type="hidden" name="type" value="1">
		  <input type="hidden" name="ready" value="1">
		  <input type="submit" value="增加">
		</form>
		<?php 
	}
	else // 上传
	{
		$content = $_GET['content'] ?? '';
		!empty($content) or die('die at line:' . __LINE__);
		$str = sprintf("insert into message values(0, ?, %d, '%s', '%s')",  
				$_SESSION['userId'], $_SESSION['username'], date('Y-m-d H:i:s'));
		$stat = getPdo()->prepare($str);
		$stat->execute([$content]);
		$stat->rowCount() > 0 or die('die at line:' . __LINE__);
		echo 'content input:', $content, '<br />';
		echo 'message successfully created. <br />';
	}
}
else if($type == 2) // 删
{
	isset($_GET['id']) or die('die at line:' . __LINE__);
	$stat = getPdo()->prepare('delete from message where id = ?');
	$stat->execute([$_GET['id']]);
	$stat->rowCount() > 0 or die('line:' . __LINE__);
	echo 'message successfully deleted. <br />';
}
else if($type == 3) // 改
{
	isset($_GET['id']) or die('die at line:' . __LINE__);
	if(!isset($_GET['ready'])) // 未上传参数
	{
		$stat = getPdo()->prepare('select * from message where id=?');
		$stat->execute([$_GET['id']]);
		$r = $stat->fetch();
		$content = $r['content'] ?? '';	
		?>
		<form>
		  content <input type="text" name="content" value="<?php echo  $content; ?>"><br />
		  <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		  <input type="hidden" name="type" value="3">
		  <input type="hidden" name="ready" value="1">
		  <input type="submit" value="编辑">
		</form>
		<?php 
	}
	else
	{
		$content = $_GET['content'] ?? '';
		!empty($content) or die('die at line:' . __LINE__);
		$stat = getPdo()->prepare('update message set content=?,date=? where id=?;');
		$stat->execute([$content, date('Y-m-d H:i:s'), $_GET['id']]);
		$stat->rowCount() > 0 or die('die at line:' . __LINE__);
		echo 'content input:', $content, '<br />';
		echo 'message successfully updated. <br />';
	}
}
?>
</body></html>
