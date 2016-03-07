<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$where=array();
$sort=request('sort','');
//当前导航
$nid=isset($_nav[MODNAME.'/'.ACTNAME]['id'])?$_nav[MODNAME.'/'.ACTNAME]['id']:0;
!empty($nid) && $where[]='channel='.$nid;
$where[]='status=1';
//当前分类
$cat=intval(request('cat',0));
//产品列表
$start = intval(request('start',0));
//判断是否显示过期商品
if($_webset['base_showover']!=1){
	$where[]='end>'.$_timestamp;
}
//是否显示明日预告
if($_webset['base_tomorrow']!=1){
	$where[]='start<'.strtotime('tomorrow');
}
$where[]='channel!='.brandNid();
$result=goodslist($where,goodssort($sort),$start,PAGE);
$goodslist=array();
if (!empty($result))
{
	if(in_array($sort,array('new','hot'))){
		$result['urls']['sort']=$sort;
	}
	$page_url=u('goods',ACTNAME,$result['urls']);
	$pages=get_page_number_list($result['total'], $start,PAGE);
	$goodslist=$result['data'];
}
//分类
$catlist=getgoodscat();
$goodscat=!empty($cat)?$catlist['cid_'.$cat]['title']:'';
?>