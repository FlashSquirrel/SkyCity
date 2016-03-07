<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

//专场模板
define('SPECIAL_TPL','double11');
define('SPECIAL_TPL_PATH',PATH_APP.'/template/'.SPECIAL_TPL);

$start = intval(request('start',0));
$result=goodslist(array('status=1','aid<0'),'',$start,30);
$goodslist=array();
if (!empty($result))
{
	$page_url='?mod='.MODNAME.'&ac='.ACTNAME.'&op='.$op.'&'.$result['url'];
	$pages=get_page_number_list($result['total'], $start, 30);
	$goodslist=$result['data'];
}

require tpl_extend(SPECIAL_TPL.'/index.tpl.php');
/* End of file index.act.php */