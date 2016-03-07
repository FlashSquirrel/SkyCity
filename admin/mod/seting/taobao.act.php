<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @taobao.act.php
 * =================================================
*/
if(submitcheck('taobaoset')){
	$taobao=request('taobao',array());
	system::webset($taobao);
	show_message('操作成功','设置成功','-1');
}
/* End of file taobao.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\seting\taobao.act.php */