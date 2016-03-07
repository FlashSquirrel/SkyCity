<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @goods.act.php
 * =================================================
*/
if(submitcheck('goodsset')){
	$goods=request('goods',array());
	system::webset($goods);
	show_message('操作成功','设置成功','-1');
}
/* End of file goods.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\seting\goods.act.php */