<?php

if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$iid=request('iid');
$gid=request('gid');
if(!empty($iid)){
	$good=getiidgood($iid);
}elseif (!empty($gid)){
	$good=getgood($gid);
}
if(empty($iid) && empty($gid)){
	message('-1','提示','商品不存在');
}
if(empty($good)){
	message('-1','提示','商品不存在');
}
//确定商品tag
$_navtag=$_id_nav[$good['channel']]['tag'];
//宝贝分类
$catlist=getgoodscat();
$good['catname']=$catlist['cid_'.$good['cat']]['title'];
//销量
if(empty($good['volume'])){
	$taokegood=get_taoke($good['num_iid']);
	$good['volume']=$taokegood['volume'];
	if(!empty($good['volume'])){
		goodAdd(array('volume'=>$good['volume'],'id'=>$good['id']));
	}
}
//你可能喜欢的宝贝
function youlikegood($cat,$id=0){
	global $_timestamp,$_webset;
	$query = lib_database::rquery('select * from '.tname('goods').' where id!=\''.$id.'\' and status=1 and cat=\''.$cat.'\' and end>'.$_timestamp.' LIMIT 0,6');
	$data=array();
	while ($rt = lib_database::fetch_one())
	{
		$data[] = $rt;
	}
	return $data;
}
?>