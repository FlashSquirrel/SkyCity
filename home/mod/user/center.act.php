<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$op=request('op');
$op=in_array($op,array('all','plus','reduce'))?$op:'all';
//积分记录
switch ($op){
	case 'all':
		$where[]='uid='.$user['uid'];
		break;
	case 'plus':
		$where[]='uid='.$user['uid'].' AND integ>0';
		break;
	case 'reduce':
		$where[]='uid='.$user['uid'].' AND integ<0';
		break;
	default:
		$where[]='uid='.$user['uid'];
		break;
}
$start = intval(request('start',0));
$result=changeloglist($where,'`addtime` desc',$start,30,false);
$log=array();
if (!empty($result))
{
	$page_url=u('user','center',array('op'=>$op));
	$pages=get_page_number_list($result['total'], $start, 30);
	$log=$result['data'];
}
?>