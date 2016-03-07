<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

require PATH_EXTEND.'/lib/common.fun.php';
$ops=array('list','add');
$op=request('op','list',$ops);
//活动
$apply_type=get_apply_type();
if($op=='list'){
	$activity_list=get_activity();
}elseif ($op=='add'){
	if(submitcheck('activityadd')){
		$activity=request('activity',array());
		//检查标题是否为空
		if(empty($activity['title']))
		{
			show_message('系统提示','活动名称不能为空',-1);
		}
		//遍历收费明细，检查提交的数据是否符合要求
		if(!empty($activity['paydetail']))
		{
			foreach($activity['paydetail']['money'] as $key=>$value)
			{
				//检查收费明细标题是否为空
				if(empty($activity['paydetail']['title'][$key]))
				{
					show_message('系统提示','收费明细标题不能为空',-1);
				}
				//检查收费明细金额是否为空，如果为空则初始化为0
				if(empty($value))
				{
					$activity['paydetail']['money'][$key]=0;
				}
			}
		}
		//判断此类型是否已存在
		$type=$activity['type'];
		if(check_type_hade($type) && empty($activity['aid'])){
			show_message('系统提示','活动类型的活动已经存在',-1);
		}
		list($activity['type'], $activity['tid']) =explode('_',$type);
		//处理付费
		$activity['pay']=intval($activity['pay']);
		$activity['free']=intval($activity['free']);
		if($activity['pay']==1){
			$activity['paydetail']=serialize($activity['paydetail']);
		}else{
			unset($activity['paydetail']);
		}
		$aid=save_activity($activity);
		show_message('系统提示','活动保存成功',$_extend_url.'&pmod=activity&op=add&aid='.$aid);
	}else{
		$activity=array();
		$aid=intval(request('aid',0));
		if(!empty($aid)){
			$activity=get_activity_info($aid);
		}
	}
}
/* End of file activity.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\admin\mod\seller\activity.act.php */