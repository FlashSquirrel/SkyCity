<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
function hook_home_register($uid){
	global $_webset;
	//注册赠送积分-------------------------------
	$reward_register=intval($_webset['reward_register']);
	if($reward_register>0){
		//积分记录
		changelog(array('uid'=>$uid,'integ'=>$reward_register,'type'=>'reward','exp'=>'注册赠送'));
		lib_database::update('users_'.APPNAME.'_fields',array('integral'=>$reward_register),'uid='.$uid);
	}
}
function hook_home_falselogin($data){
	global $uid;
	//邀请记录
	$usertag=lib_request::$cookies['usertag'];
	if(!empty($usertag)){
		$invitelog=array('usertag'=>$usertag,'tuser_name'=>$data['api'].$data['user_name'],'tuid'=>$uid);
		invitelog($invitelog);
	}
}
