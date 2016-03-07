<?php
/**
 * =================================================
 * @login.act.php
 * =================================================
*/
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
/*登陆*/
$username=request('username');
$password=request('password');
if(!empty($username) && !empty($password))
{
	//验证码
	$validate=request('validate');
	if(strtolower($validate)!=strtolower($_SESSION['ckstr'])){
		$errmsg = '验证码错误！';
	}else{
		$rs = $access->check_user($username, $password);
		if($rs['code']==1){
			$jumpurl = empty($gourl) ? '?mod=index' : $gourl;
			show_message ('成功登录', '成功登录，正在重定向你访问的页面',$jumpurl);
		}else{
			$errmsg=$rs['msg'];
		}
	}
}elseif (request('adminsubmit')){
	$errmsg = '请输入登录帐号和密码！';
}

?>