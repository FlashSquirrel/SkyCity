<?php

if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$activity_list=get_activity();
require tpl_extend('index.tpl.php');
?>