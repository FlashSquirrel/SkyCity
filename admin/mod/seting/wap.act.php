<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
if(submitcheck('wapset')){
	$wap=request('wap',array());
	system::webset($wap);
	show_message('操作成功','设置成功','-1');
}
/* End of file wap.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\seting\wap.act.php */