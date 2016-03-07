<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
if(submitcheck('formhash')){
	$reg=request('reg',array());
	if(!empty($reg)){
		//注册开始
		try
		{
			//判断验证码
			if(strtolower($reg['verify'])!=strtolower($_SESSION['ckstr'])){
				$error='验证码错误！';
			}
			//判断密码
			elseif($reg['reuserpwd']!=$reg['userpwd']){
				$error='确认密码错误';
			}else{
				unset($reg['verify'],$reg['reuserpwd']);
				$rs = register($reg);
				if( $rs>0)
				{
					//判断是否注册需要激活
					if($_webset['site_activation']==1){
						//发送激活邮件
						send_register_email($reg['email'],$rs,array('username'=>$reg['user_name']));
						$error='注册邮件已发出,请激活';
					}else{
						$rs = $access->check_user($reg['email'], $reg['userpwd']);
						if( $rs['code']==1 )
						{
							header('location:'.u(MODNAME,'index'));
							exit();
						}
						if($rs['code']!=1){
							$error=$rs['msg'];
						}
					}
				}else{
					$error=$errmsg;
				}
			}
		}
		catch ( Exception $e )
		{
			//错误提示
			$errmsg = $e->getMessage();
			$error=$errmsg;
		}
	}
}
require tpl_extend(WAP_TPL.'/register.tpl.php');
/* End of file register.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\mod\register.act.php */