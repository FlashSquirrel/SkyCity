<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$gourl = request('gourl');
if(empty($gourl))
{
	$gourl =base64_encode(u('seller','account'));
}
if(submitcheck('login_form')){
	$login=request('login');
	if( !empty($login['email']) && !empty($login['userpwd']) )
	{
		$gourl=base64_decode($gourl);
		$rs = $access->check_user($login['email'], $login['userpwd']);
		if( $rs['code']==1)
		{
			extend_message(0,'登陆提示','登陆成功！',$gourl);
		}
		if($rs['code']!=1){
			extend_message(-1,'登陆提示',$rs['msg']);
		}
	}
	//登陆界面数据处理快捷登陆
	else{
		extend_message(-1,'登陆提示',"请填写账号和密码");
	}
}
require tpl_extend('login.tpl.php');
/* End of file login.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\mod\merchant\login.act.php */