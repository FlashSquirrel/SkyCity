<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$op=request('op','index');
$inviteurl=getinviteurl();
if($op=='log'){
	$start = intval(request('start',0));
	$result=inviteloglist(array('usertag=\''.$user['tag'].'\''),'`addtime` DESC',$start,30);
	$goodslist=array();
	if (!empty($result))
	{
		$page_url=u('user','invite',array('op'=>$op));
		$pages=get_page_number_list($result['total'], $start,30);
		$invitelog=$result['data'];
	}
}
?>