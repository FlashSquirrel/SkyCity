<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$id=intval(request('id',0));
if(empty($id)){
	message('-1','提示',"兑换不存在",u(MODNAME,'index'));
}
$exchange=getexc($id);
if(empty($exchange)){
	message('-1','提示',"兑换不存在",u(MODNAME,'index'));
}
//兑换记录
$commentresult=commentlist($exchange['id'],'exchange');
$commentlist=array();
if (!empty($commentresult))
{
	$page_url=u('goods','detail',$commentresult['urls']);
	$pages=get_page_number_list($commentresult['total'], request('start',0),CPAGE);
	$commentlist=$commentresult['data'];
}
require tpl_extend('detail.tpl.php');
/* End of file detail.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\gift\mod\detail.act.php */