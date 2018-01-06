<!DOCTYPE html>
<html>
<head> <title>TinyBoard | login</title> </head><body>
<a href="index.php">Home</a> <br />

<?php
// 用户登录和登出
session_start();
if(isset($_SESSION['username']))
{
	isset($_GET['logout']) or die('die at line:' . __LINE__);
	// 退出登录
	$_SESSION = [];
	if(isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-1, '/');
	session_destroy();
	echo 'logout successfully <br />';
	goto End;
}

if(!isset($_POST['username']) || !isset($_POST['passwd']))
{
	?> 
	<form method="post">
    user name: <input type="text" name="username" value="Huyelei"> <br />
    password : <input type="password" name="passwd" value="123"> <br />
    <input type="submit" value="Login">
    </form>
	<?php
	goto End;
}
	
$username = $_POST['username'];
$password = $_POST['passwd'];
$query = "select * from user where name='$username' and passwd='$password'";

require 'cfg.php';
require 'util.php';
$stat = getPdo()->prepare($query);
$stat->execute();
$r = $stat->fetch(PDO::FETCH_ASSOC);
!empty($r) && isset($r['name']) or die('die at line:' . __LINE__);

// 登录成功
$_SESSION['username'] = $r['name'];
$_SESSION['userId'] = $r['id'];
echo 'user:', $_SESSION['username'],' login successfully.', '<br />';
	
End:
?>
</body></html>