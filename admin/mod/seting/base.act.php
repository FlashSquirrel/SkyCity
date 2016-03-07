<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @base.act.php
 * =================================================
*/
if(submitcheck('baseset')){
	$base=request('base',array());
	system::webset($base);
	show_message('操作成功','设置成功','-1');
}
/* End of file base.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\seting\base.act.php */