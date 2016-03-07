<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

//查询
$start = intval(request('start',0));
$page=empty($start)?1:ceil($start/20)+1;
$where[]='`start`>=\''.strtotime('tomorrow').'\'';
$where[]='channel!='.brandNid();
$result=goodslist($where,'',$start,20);
$goodslist=array();
if (!empty($result))
{
	$num=ceil($result['total']/20);
	$pages=get_page_number_list($result['total'], $start,20);
	$goods=$result['data'];
}
require tpl_extend(WAP_TPL.'/index.tpl.php');
/* End of file tomorrow.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\mod\tomorrow.act.php */