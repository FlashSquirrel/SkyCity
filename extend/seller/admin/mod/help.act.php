<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

require PATH_EXTEND.'/lib/common.fun.php';
$ops=array('list','add');
$op=request('op','list',$ops);
//报名指南
if($op=='list'){
	$guide_list=get_guide_list();
}
//添加指南
elseif ($op=='add'){
	if(submitcheck('guide_form')){
		$guide=request('guide',array());
		if(empty($guide['title'])){
			show_message('添加失败','请填写文章标题',-1);
		}
		if(!empty($guide)){
			save_guide($guide);
			show_message('添加成功','文章添加成功',$_extend_url.'&pmod=help');
		}
	}else{
		$gid=request('gid',0);
		$guide=get_guide($gid);
	}
}

/* End of file help.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\admin\mod\merchant\help.act.php */