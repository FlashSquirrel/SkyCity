<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
$ops=array('mytpl','tplset');
$op=request('op','mytpl');
//我的模板
$tpls=getTpl();
if(submitcheck('tplset')){
	$tpl=request('tpl');
	system::webset($tpl,true);
	show_message('操作成功','设置成功','?mod='.MODNAME.'&ac='.ACTNAME.'&op='.$op);
}
if ($op=='tplset'){
	$tpl=$tpls[$_webset['site_tpl']];
	//扩展模板设置项目
	$extend_tpl_set=get_extend_tpl_set();
}
/* End of file tpl.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\seting\tpl.act.php */