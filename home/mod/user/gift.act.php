<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$op=request('op','try');
$op=in_array($op,array('try','exchange'))?$op:'try';
$start = intval(request('start',0));
//申请兑换记录
$result=applylog(array('a.uid='.$user['uid'],'a.idtype=\''.$op.'\''),'`addtime` desc',$start,30,false);
$trylist=array();
if (!empty($result))
{
	$page_url=u('user','gift',array('op'=>$op));
	$pages=get_page_number_list($result['total'], $start, 30);
	$applylog=$result['data'];
}

?>