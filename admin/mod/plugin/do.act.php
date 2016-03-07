<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @do.act.php
 * =================================================
*/
$identifier=request('identifier');
$pmod=request('pmod');
if(empty($identifier) || empty($pmod)){
	show_message('系统提示','操作错误',-1);
	exit();
}
$_plugin_url='?mod='.MODNAME.'&ac='.ACTNAME.'&identifier='.$identifier;
define('PATH_PLUGIN', PATH_ROOT.'plugin/'.$identifier.'/');
//资源目录
define('PATH_STATIC', '../plugin/'.$identifier.'/static/');
//调用数据
include PATH_PLUGIN.'admin/mod/'.$pmod.'.act.php';
include PATH_PLUGIN.'admin/template/'.$pmod.'.tpl.php';
exit();
