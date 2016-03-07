<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @system.act.php
 * =================================================
*/
$ops=array('sys','sensitive');
$op=request('op','sys',$ops);
if(submitcheck('sysset')){
	$sys=request('sys',array());
	system::webset($sys);
	show_message('操作成功','设置成功','-1');
}
if($op=='sensitive'){
	if(submitcheck('sensitiveset')){
		$sensitive=request('sensitive');
		sensitive($sensitive);
		show_message('敏感词设置','敏感词设置成功',-1);
	}else{
		$sensitive=require(FILTER_WORD);
	}
}
/* End of file system.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\seting\system.act.php */