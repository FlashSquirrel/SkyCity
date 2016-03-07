<?php

if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$start = intval(request('start',0));
$result=goodsfavlist(array('a.`uid`='.$user['uid']),'a.`addtime` DESC',$start,30);
$favlist=array();
if (!empty($result))
{
	$page_url=u('user','fav');
	$pages=get_page_number_list($result['total'], $start,30);
	$favlist=$result['data'];
}
?>