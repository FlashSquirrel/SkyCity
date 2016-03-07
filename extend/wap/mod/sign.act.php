<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$hash=request('hash','');
if (formhash()==$hash){
	//判断是否已经签到
	if($user['lastsign']<strtotime('today')){
		//获取当天签到所得积分
		$integral=sign();
	}
	header('location:'.u(MODNAME,'sign'));
	exit();
}
$weekarray=array("日","一","二","三","四","五","六");
//签到记录
$beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
$endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
$query=lib_database::rquery('select * from '.tname('users_changelog').' where type=\'sign\' and addtime>='.$beginThismonth.' and addtime<='.$endThismonth.' and uid='.$user['uid']);
while ($value=lib_database::fetch_one()){
	$sigin_log[date('j',$value['addtime'])]=$value;
}
require tpl_extend(WAP_TPL.'/sign.tpl.php');
/* End of file sign.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\mod\sign.act.php */