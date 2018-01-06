<?php 
/* TinyBoard, 一个500行的留言板程序
   author huyelei@yeah.net 2018.01.05 QQ群619348997
   使用方法：
    1 所有文件拷贝到Web根目录
    2 执行install.php, 实现数据库导入和配置文件cfg.php的创建
    3 手工删除install.php, 访问index.php。
    Enjoy.
*/ 
?>
<!DOCTYPE html>
<html><head>
<meta charset="UTF-8">
<title>TinyBoard</title>
<style type="text/css">
.header {
	width:820px;
	margin: auto;
}
.sheet {
	display:table;
	border-collapse:separate;
	margin:auto;
}
.row { display:table-row; }
.row div { display:table-cell; } /* row类中的 div标签 */
.row .one { width:80px; }        /* row类中的第一列 */
.row .two { 
    width:320px; 
    max-width:320px;
 	white-space:nowrap;
	overflow:hidden;
	text-overflow: ellipsis;
}       
.row .three { width:96px; }
.row .four { 
    width: 200px;  
    max-width:200px;
 	white-space:nowrap;
	overflow:hidden;
	text-overflow: ellipsis;
 }
.row .five { width: 120px; }

.leg {   /* 页码 */
	width:800px;
	text-align:right;
	margin: auto;
}
</style>
</head><body>

<?php
session_start();
echo '<div class="header">';
if(!isset($_SESSION['username']))
	echo '<a href="login.php">登录</a>';
else
	echo $_SESSION['username'], ' <a href="login.php?logout=1">退出登录</a> ', ' <a href="manage.php?type=1">发帖</a> ';
echo '</div><br />';
require 'cfg.php';
require 'util.php';

$pdo = getPdo();
$stat = $pdo->query('select count(1) from message');
$r = $stat->fetch();
$r['count(1)'] > 0 or die('die at line:' . __LINE__);

$lineSum = $r['count(1)'];
$pageSum = 1 + (int)($lineSum - 1)/LINE_PER_PAGE;
$page = truncate($_GET['page'], $pageSum - 1); // 当前页, 从0开始
$lineStart = (int) $page * LINE_PER_PAGE;      // 开始行，从0开始
$lineEnd = min($lineSum - 1, $lineStart + LINE_PER_PAGE - 1);
$lineNum = $lineEnd - $lineStart + 1;

$str = sprintf('select * from message order by id desc limit %s, %s;', $lineStart, $lineNum);
$stat = $pdo->query($str);
$items = $stat->fetchAll(); 

$horizonLine =<<<HTML
    <div class="row">
        <div class="one">----------</div>
        <div class="two">----------------------------------------</div>
        <div class="three">------------</div>
        <div class="four">-------------------------</div>
        <div class="five">---------------</div>
    </div>
HTML;
?>

<div class="sheet">
	<?php echo $horizonLine; ?>
    <div class="row">
        <div class="one">&nbsp;ID</div>
        <div class="two">内容</div>
        <div class="three">&nbsp;用户</div>
        <div class="four">时间</div>
        <div class="five">操作</div>
    </div>
	<?php echo $horizonLine; ?>

    <?php foreach ($items as $item): ?>
    <div class="row">
        <div class="one">&nbsp;<?php echo $item['id'] ?></div>
        <div class="two"><?php echo $item['content'] ?></div>
        <div class="three">&nbsp;<?php echo $item['authorName'] ?></div>
        <div class="four"><?php echo $item['date'] ?></div>
        <div class="five"> <?php
	        $url = 'detail.php?id=' .  $item['id'];
	        echo ' <a href="' . $url . '">详情</a> ';
	        if(isset($_SESSION['userId']) && $_SESSION['userId'] == $item['author'])
	        {
	        	$delete = 'manage.php?type=2&id=' .  $item['id'];
	        	$update = 'manage.php?type=3&id=' .  $item['id'];
	        	echo ' <a href="' . $delete . '">删除</a> ';
	        	echo ' <a href="' . $update . '">编辑</a> ';
	        }
        ?></div>
    </div>
    <?php endforeach; ?>
	<?php echo $horizonLine; ?>
</div>

<div class="leg">
<?php 
$chapt = (int)($page/PAGE_PER_CHAPTER); // 当前chapter, 从0开始
$chaptSum =  1 + (int)(($pageSum - 1)/PAGE_PER_CHAPTER);
$pageStart = (int)($chapt * PAGE_PER_CHAPTER);
$pageEnd = min($pageSum - 1, $pageStart + PAGE_PER_CHAPTER - 1);
$pageNum = $pageEnd - $pageStart + 1;
// echo '$page:', $page, ' $chap:', $chapt, '<br />';
if($chapt > 0)
{
	$pagePriv = (int)(($chapt - 1)*PAGE_PER_CHAPTER);
	$url = 'index.php?page=' . $pagePriv;
	echo ' <a href="' . $url . '">' . '&lt;</a> ';
}
for($i=0; $i<$pageNum; $i++)
{
	$id = $pageStart + $i;
	$url = 'index.php?page=' . $id;
	echo ' <a href="' . $url . '">' . ($id+1). '</a> ';
}
if($chapt < ($chaptSum - 1))
{
	$pageNext = (int)(($chapt + 1)*PAGE_PER_CHAPTER);
	$url = 'index.php?page=' . $pageNext;
	echo ' <a href="' . $url . '">' . '&gt;</a> ';
}
?>
</div>
</body></html>
