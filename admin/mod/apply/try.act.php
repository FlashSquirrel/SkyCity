<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
$ops=array('list','refuse');
$op=request('op','list',$ops);
//宝贝列表
if($op=='list'){
	$start = intval(request('start',0));
	$result=trylist(array('status=0'),'`start` ASC',$start,30);
	$trylist=array();
	if (!empty($result))
	{
		$page_url='?mod=try&ac='.request('ac').'&op=list&'.$result['url'];
		$pages=get_page_number_list($result['total'], $start, 30);
		$trylist=$result['data'];
	}
}
//拒绝的
elseif ($op=='refuse'){
	$start = intval(request('start',0));
	$result=trylist(array('a.`status`=-1'),'a.`addtime` ASC',$start,30,true);
	$trylist=array();
	if (!empty($result))
	{
		$page_url='?mod=try&ac='.request('ac').'&op=refuse&'.$result['url'];
		$pages=get_page_number_list($result['total'], $start, 30);
		$trylist=$result['data'];
	}
}
/* End of file try.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\apply\try.act.php */