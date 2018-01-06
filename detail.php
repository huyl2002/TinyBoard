<?php 
   // 消息细节页面 
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>TinyBoard | detail</title>
</head>
<body>
<a href="index.php">首页</a><br /><br />
<?php 
session_start();
require 'cfg.php';
require 'util.php';
isset($_GET['id']) or die('die at line:' . __LINE__);
$stat = getPdo()->prepare('select * from message where id = ?');
$stat->execute([$_GET['id']]); 
$item = $stat->fetch();
	
extract($item);
echo 'id: ', $id, '<br />'; 
echo 'content: ', $content, '<br />';
echo 'authorId: ', $author, '<br />';
echo 'authorName: ', $authorName, '<br />';
echo 'date: ', $date, '<br />';

if(isset($_SESSION['userId']) && $_SESSION['userId'] == $item['author'])
{
	echo '<br />';
	$delete = 'manage.php?type=2&id=' .  $item['id'];
	$update = 'manage.php?type=3&id=' .  $item['id'];
	echo ' <a href="' . $delete . '">删除</a> ';
	echo ' <a href="' . $update . '">编辑</a> ';
}

?>
</body>
</html>