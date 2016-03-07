<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @brand.act.php
 * =================================================
*/
$ops=array('list','add');
$op=request('op','list',$ops);

if($op=='list'){
	$start = intval(request('start',0));
	$result=brandlist(array(),'`sort` desc,`start` DESC',$start,30);
	$brandlist=array();
	if (!empty($result))
	{
		$page_url='?mod='.MODNAME.'&ac='.ACTNAME.'&op='.$op.'&'.$result['url'];
		$pages=get_page_number_list($result['total'], $start, 30);
		$brandlist=$result['data'];
	}
}
elseif ($op=='add'){
	$brand=request('brand');
	$bid=request('bid',0);
	if(!empty($brand)){
		if(empty($brand['brand'])){
			show_message('系统提示','请填写品牌名称',-1);
		}
		if(empty($brand['logo'])){
			show_message('系统提示','请设置品牌logo',-1);
		}
		if(empty($brand['url'])){
			show_message('系统提示','请设置店铺地址',-1);
		}
		if(empty($brand['nick'])){
			show_message('系统提示','请填写店铺昵称',-1);
		}
		if(empty($brand['remark'])){
			show_message('系统提示','请填写品牌说明',-1);
		}
		if(empty($brand['pic'])){
			show_message('系统提示','请设置品牌广告',-1);
		}
		$brand['end']=strtotime($brand['end']);
		$brand['start']=strtotime($brand['start']);
		brand($brand);
		if(!empty($brand['bid'])){
			show_message('系统提示','品牌修改成功','?mod='.MODNAME.'&ac='.ACTNAME);
		}else{
			show_message('系统提示','品牌添加成功','?mod='.MODNAME.'&ac='.ACTNAME);
		}
	}
	elseif (!empty($bid)){
		$brand=getbrand($bid);
	}
}
/* End of file brand.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\main\brand.act.php */