<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$guide_list=get_guide_list();
$gid=request('gid',0);
if(!empty($guide_list) && empty($gid)){
	reset($guide_list);
	$guide=current($guide_list);
}else{
	$guide=$guide_list[$gid];
}
require tpl_extend('help.tpl.php');
/* End of file help.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\mod\merchant\help.act.php */