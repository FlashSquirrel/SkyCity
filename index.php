<?php

if( !file_exists(dirname(__FILE__) . "/data/install.lock") )
{
	header("Location:install/index.php");
	exit();
}
require './library/init.php';
require PATH_APP.'/common.inc.php';
//调用
require execute_mod(MODNAME,ACTNAME);
if(!defined('IS_EXTEND') && !defined('IS_PLUGIN')){
	require tpl_mod(MODNAME,ACTNAME);
}
?>