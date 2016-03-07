<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @package		D:\website\taobaoke\jiu2\admin\mod\advertise\link.act.php
 * @author		bank
 * @link.act.php
 * =================================================
*/
$ops=array('linkList','linkAdd');
$op=request('op','linkList',$ops);
$active[$op]='class="active"';

$link=request('link',array());
if(!empty($link)){
	system::linkset($link);
	show_message('友情链接','友情链接设置成功','?mod='.MODNAME.'&ac='.ACTNAME);
}
$id=request('id');
if(!empty($id)){
	$link=system::getlink();
	$link=$link[$id];
}
/* End of file link.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\advertise\link.act.php */