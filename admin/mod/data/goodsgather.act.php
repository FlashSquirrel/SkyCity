<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @goodsgather.act.php
 * =================================================
*/
$ops=array('list','addtask','do');
$op=request('op','list',$ops);

//导航频道
$goodnav=navList();
//系统分类
$catlist=getgoodscat();
if($op=='list'){
	$tasklist=tasklist();
}elseif ($op=='addtask'){
	//添加采集规则
	$task=request('task');
	if(!empty($task)){
		addgather($task);
		show_message('操作成功','添加规则成功','?mod='.MODNAME.'&ac='.ACTNAME);
	}else{
		$tid=request('tid');
		$task=array();
		if(!empty($tid)){
			$task=gettask($tid);
			$task=$task[0];
		}
	}
}
//采集
elseif ($op=='do'){
	$tid=request('tid');
	$task=gettask($tid);
}
/* End of file goodsgather.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\data\goodsgather.act.php */