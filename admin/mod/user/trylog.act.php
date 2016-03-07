<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
$ops=array('apply','ship','show','fail');
$op=request('op','apply',$ops);

$start = intval(request('start',0));
$where[]='a.idtype=\'try\'';
$sort='a.`addtime` ASC';
if($op=='apply'){
	$where[]='a.status=0';
}elseif ($op=='ship'){
	$where[]='(a.status=1 or a.status=2)';
}elseif ($op=='show'){
	$where[]='a.status=3';
}elseif ($op=='fail'){
	$where[]='a.status=-1';
}
$result=applylog($where,$sort,$start,30);
$trylist=array();
if (!empty($result))
{
	$page_url='?mod='.MODNAME.'&ac='.ACTNAME.'&op='.$op.'&'.$result['url'];
	$pages=get_page_number_list($result['total'], $start, 30);
	$applylog=$result['data'];
}
/* End of file trylog.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\user\trylog.act.php */