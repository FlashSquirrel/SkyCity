<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @cat.act.php
 * =================================================
*/
$ops=array('list','add');
$op=request('op','list',$ops);
$cat=request('cat',array());
if(!empty($cat)){
	//添加
	typeAdd($cat);
	show_message('操作成功','栏目设置成功','?mod='.MODNAME.'&ac='.ACTNAME);
}
$catlist=getarticlecat('article');
$id=request('id',0);
if(!empty($id)){
	$cat=$catlist['cid_'.$id];
}
/* End of file cat.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\article\cat.act.php */