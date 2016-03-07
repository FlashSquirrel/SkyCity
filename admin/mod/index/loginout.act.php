<?php
/**
 * =================================================
 * @loginout.act.php
 * =================================================
*/
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}

$access->loginout();
show_message ('退出成功', '退出成功，正在重定向访问的页面','?mod=index&ac=login');
?>