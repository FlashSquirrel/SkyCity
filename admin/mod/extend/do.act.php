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
$_extend_url='?mod='.MODNAME.'&ac='.ACTNAME.'&identifier='.$identifier;
define('PATH_EXTEND', PATH_ROOT.'extend/'.$identifier);
define('PATH_EXTEND_TPL', PATH_ROOT.'extend/'.$identifier.'/admin/template');
//资源目录
define('PATH_STATIC', '../extend/'.$identifier.'/static');
system::check($identifier,'extend');
//调用数据
include PATH_EXTEND.'/admin/mod/'.$pmod.'.act.php';
include PATH_EXTEND.'/admin/template/'.$pmod.'.tpl.php';
exit();
/* End of file do.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\admin\mod\extend\do.act.php */