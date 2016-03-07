<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

if(submitcheck('forgetform')){
	$forget=request('forget');
	if(!empty($forget)){
		//判断验证码
		if(strtolower($forget['verify'])!=strtolower($_SESSION['ckstr'])){
			$error='验证码错误！';
		}elseif (empty($forget['email'])){
			$error='请输入邮箱\账号';
		}else{
			//发送邮件找回密码
			if($_webset['site_activation']==1){
				if(lib_validate::email($forget['email'])){
					$files=check_account_exist($forget['email']);
				}else{
					$files=check_account_exist($forget['email'],'user_name');
				}
				if(!empty($files['email'])){
					$error='用户不存在';
				}else{
					//发送找回密码邮件
					send_forget_email($files['email'],$files['uid'],array('user_name'=>$files['user_name'],'url'=>u(MODNAME,'callback',array('op'=>'fgcall','code'=>$code)),'code'=>$code));
					$error='找回密码邮件已发出';
				}
			}else{
				$error='请联系管理员，找回密码';
			}
		}
	}
}
require tpl_extend(WAP_TPL.'/forget.tpl.php');
/* End of file forget.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\mod\forget.act.php */