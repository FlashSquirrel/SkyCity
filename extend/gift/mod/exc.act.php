<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$start = intval(request('start',0));
$result=exclist(array('status=1'),'`start` desc',$start,PAGE);
$exchangelist=array();
if (!empty($result))
{
	$page_url=u(MODNAME,'exc');
	$pages=get_page_number_list($result['total'], $start, PAGE);
	$exchangelist=$result['data'];
}
require tpl_extend('exc.tpl.php');
/* End of file exc.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\gift\mod\exc.act.php */