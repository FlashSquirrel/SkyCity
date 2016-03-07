<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
//明日预告
function tomorrow_goods($sort,$start,$num=PAGE){
	global $_timestamp,$_webset;
	$sort=goodssort($sort);
	$where[]='status=1';
	$where[]='start>='.strtotime('tomorrow');
	$where[]='start<'.strtotime('tomorrow')+3600*24;
	$result=goodslist($where,$sort,$start,$num);
	$goodslist=array();
	if (!empty($result))
	{
		if(in_array($sort,array('new','hot'))){
			$result['urls']['sort']=$sort;
		}
		$page_url=u('index','tomorrow',$result['urls']);
		$pages=get_page_number_list($result['total'], $start,$num);
		$goodslist=$result['data'];
		return array('pages'=>$pages,'page_url'=>$page_url,'data'=>$goodslist);
	}
	return array();
}
?>