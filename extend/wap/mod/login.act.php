<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

if(submitcheck('loginform')){
	$login=request('login');
	if( !empty($login['email']) && !empty($login['userpwd']) )
	{
		$keeptime = isset($login['save']) ? 86400 : 0;
		$rs = $access->check_user($login['email'], $login['userpwd'], $keeptime);
		if( $rs['code']==1 )
		{
			header('location:'.u(MODNAME,'sign'));
			exit();
		}
		if($rs['code']!=1){
			$error=$rs['msg'];
		}
	}
	if(empty($login['email'])){$error='请输入用户名';}
	elseif (empty($login['userpwd'])){$error='密码错误';}
}else{
	if(!empty($user['uid'])){
		header('location:'.u(MODNAME,'sign'));
		exit();
	}
	$gourl=request('gourl','');
}
require tpl_extend(WAP_TPL.'/login.tpl.php');
/* End of file login.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\mod\login.act.php */