<?php

if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
//�����Ż�
function index_goods($sort,$start,$num=PAGE){
	global $_timestamp,$_webset;
	//Ʒ����id
	$nid=brandNid();
	$goodsort=goodssort($sort);
	$where[]='status=1';
	$where[]='channel!='.$nid;
	//�ж��Ƿ���ʾ������Ʒ
	if($_webset['base_showover']!=1){
		$where[]='end>'.$_timestamp;
	}
	//�Ƿ���ʾ����Ԥ��
	if($_webset['base_tomorrow']!=1){
		$where[]='start<'.strtotime('tomorrow');
	}
	$result=goodslist($where,$goodsort,$start,$num);
	$goodslist=array();
	if (!empty($result))
	{
		if(in_array($sort,array('new','hot'))){
			$result['urls']['sort']=$sort;
		}
		$page_url=u('index','index',$result['urls']);
		$pages=get_page_number_list($result['total'], $start,$num);
		$goodslist=$result['data'];
		return array('pages'=>$pages,'page_url'=>$page_url,'data'=>$goodslist);
	}
	return array();
}
//��������
function footerlink(){
	return system::getlink();
}
?>