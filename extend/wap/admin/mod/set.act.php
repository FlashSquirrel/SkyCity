<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

require PATH_EXTEND.'/lib/common.fun.php';
if(submitcheck('wapset')){
	$wap=request('wap',array());
	system::webset($wap,true);
	show_message('操作成功','设置成功','-1');
}
$wap_tpl=get_wap_tpl();
/* End of file set.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\admin\mod\set.act.php */