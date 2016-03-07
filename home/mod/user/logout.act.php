<?php

if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$access->loginout();
message('0','退出成功','成功退出',u('index','index'));
?>