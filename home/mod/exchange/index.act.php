<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$start = intval(request('start',0));
$result=exclist(array('status=1'),'`start` ASC',$start,PAGE);
$exchangelist=array();
if (!empty($result))
{
	$page_url=u('exchange','index',$result['urls']);
	$pages=get_page_number_list($result['total'], $start, PAGE);
	$exchangelist=$result['data'];
}


?>