<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

require PATH_EXTEND.'/lib/common.fun.php';
$ops=array('list','add');
$op=request('op','list',$ops);
if($op=='list'){
	$special_list=get_special();
}elseif ($op=='add'){
	if(submitcheck('specialadd')){
		$special=request('special',array());
		if(empty($special['title'])){
			show_message('添加失败','请填写专场名称',-1);
		}
		if(empty($special['endtime'])){
			show_message('添加失败','请填写结束时间',-1);
		}
		if(save_special($special)){
			show_message('添加成功','专场添加成功',$_extend_url.'&pmod=index');
		}else{
			show_message('添加失败','请检查展位格式',-1);
		}
	}else{
		$special=array();
		$sid=intval(request('sid',0));
		if(!empty($sid)){
			$special=get_special_info($sid);
		}
	}
}
/* End of file index.act.php */