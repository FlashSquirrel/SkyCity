<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
$ops=array('list','audit');
$op=request('op','list',$ops);
//审核评论
if($op=='audit'){
	$cid=request('cid',0);
	commonaudit($cid);
	show_message('提示','审核成功','?mod='.MODNAME.'&ac='.ACTNAME);
}
//评论列表
$start = intval(request('start',0));
$result=getcomment(array('idtype=\'goods\''),'`addtime` DESC',$start,30);
$commentlist=array();
if (!empty($result))
{
	$page_url='?mod='.MODNAME.'&ac='.ACTNAME.'&'.$result['url'];
	$pages=get_page_number_list($result['total'], $start, 30);
	$commentlist=$result['data'];
}
/* End of file comment.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\user\comment.act.php */