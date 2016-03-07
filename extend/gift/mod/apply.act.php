<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$id=intval(request('id',0));
if(empty($id)){
	extend_message('-1','兑换提示',"兑换不存在",-1);
}
//判断是否登录
if(empty($user['uid'])){
	extend_message('-1','兑换提示',"请登录",u('user','login',array('gourl'=>urlencode(get_cururl()))));
}
//判断是否设置了昵称
if(empty($user['user_name'])){
	extend_message('-1','兑换提示',"请设置昵称",u('user','base',array('gourl'=>urlencode(get_cururl()))));
}
//判断有无未晒单记录
if($_webset['exchange_showinfo']==0){
	if(check_nosun('exchange')){
		extend_message('-1','兑换提示',"请您先把之前兑换晒单，再申请新的兑换",u(MODNAME,'detail',array('id'=>$id)));
	}
}
$exchangeinfo=getexc($id);
if(empty($exchangeinfo) || $exchangeinfo['apply']>=$exchangeinfo['num']){
	extend_message('-1','兑换提示',"已经兑换光了",u(MODNAME,'detail',array('id'=>$id)));
}
//判断有没有填写地址
$address=useraddress($user['uid']);
if(submitcheck('appaly')){
	if(empty($address)){
		extend_message('-1','兑换提示',"请设置收货地址",-1);
	}
	//验证积分是否足够
	if($user['integral']<$exchangeinfo['needintegral']){
		extend_message('-1','兑换提示',"积分不足",-1);
	}
	//开始兑换
	$log=$exchangeinfo;
	$log['remark']=request('remark','');
	if(exc_apply($log)){
		extend_message('0','兑换提示',"兑换成功",u(MODNAME,'index'));
	}else{
		extend_message('-1','兑换提示',"兑换失败",-1);
	}
}
require tpl_extend('apply.tpl.php');
/* End of file apply.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\gift\mod\apply.act.php */