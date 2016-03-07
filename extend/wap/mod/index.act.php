<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$where=array();
//分类
$catlist=getgoodscat();
$cat=request('cat','');
//排序
$sort='';
$g_sort=request('sort','');
if(!empty($g_sort)){
	$g_sort=='discount' && $sort='`discount` ASC';
	$g_sort=='hot' && $sort='`fav` DESC';
}
//页数
$start = intval(request('start',0));
$page=empty($start)?1:ceil($start/20)+1;
$where=array('channel!='.brandNid(),'start<'.strtotime('tomorrow'));
$result=goodslist($where,$sort,$start,20);
$goods=array();
if (!empty($result))
{
	$count=$result['total'];
	$num=ceil($count/20);
	$pages=get_page_number_list($result['total'], $start,20);
	$goods=$result['data'];
}
if($_isajax){
	if(empty($goods)){
		echo json_encode(array('isover'=>true));
		exit();
	}
	$today=strtotime('today');
	if(!empty($goods)){
		foreach ($goods as $key=>$value){
			$goods[$key]['url_format']=u('wap','jump',array('iid'=>$value['num_iid']));
			$goods[$key]['pic']=get_img($value['pic'],'290');
			if($value['start']>$today){
				$goods[$key]['is_new']=1;
			}else{
				$goods[$key]['is_new']=0;
			}
		}
	}
	echo json_encode(array('isover'=>false,'num'=>$num,'allnum'=>$allnum,'sort'=>$g_sort,'cat'=>$cat,'goods'=>$goods));
	exit();
}else{
	require tpl_extend(WAP_TPL.'/index.tpl.php');
}
/* End of file index.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\mod\index.act.php */