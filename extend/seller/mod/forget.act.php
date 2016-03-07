<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

if(submitcheck('forgetform')){
	$email=request('email','');
	if(empty($email)){
		extend_message('0','系统提示','请提填写邮箱',-1);	
	}
	if(lib_validate::email($email)){
		$files=check_account_exist($email);
	}
	if(!empty($files['email'])){
		//发送邮件
		$code=creat_code();
		send_forget_email($files['email'],$files['uid'],array('user_name'=>$files['user_name'],'url'=>u(MODNAME,'callback',array('op'=>'fgcall','code'=>$code)),'code'=>$code));
		header("location:".u(MODNAME,'callback',array('op'=>'forget','email'=>$files['email'])));
	}else{
		if(!empty($files['uid'])){
			extend_message('0','系统提示','邮箱不存在',-1);
		}
		extend_message('0','系统提示','账号不存在',-1);
	}
}
require tpl_extend('forget.tpl.php');
/* End of file forget.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\seller\mod\forget.act.php */