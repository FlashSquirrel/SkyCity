<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$ops=array('list','addblack');
$op=request('op','list',$ops);

if($op=='addblack'){
	$black=request('blacklist',array());
	if(!empty($black)){
		//判断是否存在
		$ishade=getblack($black['nick']);
		if(!empty($ishade)){
			show_message('操作提示','此用户已被加入黑名单',-1);
		}
		addblack($black);
		show_message('操作成功','黑名单添加成功',$_extend_url.'&pmod=black');
	}
}else{
	$start = intval(request('start',0));
	$result=blacklist(array(),'`addtime` DESC',$start,1);
	$blacklist=array();
	if (!empty($result))
	{
		$page_url=$_extend_url.'&pmod=black&op=list&'.$result['url'];
		$pages=get_page_number_list($result['total'], $start, 1);
		$blacklist=$result['data'];
	}
}
/* End of file black.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\admin\mod\seller\black.act.php */