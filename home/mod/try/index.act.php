<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}

$start = intval(request('start',0));
$result=trylist(array('status=1'),'`start` ASC',$start,PAGE);
$trylist=array();
if (!empty($result))
{
	$page_url=u('try','index',$result['urls']);
	$pages=get_page_number_list($result['total'], $start, PAGE);
	$trylist=$result['data'];
}
?>