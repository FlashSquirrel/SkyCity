<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
require PATH_EXTEND.'/lib/common.fun.php';
$ops=array('list','add');
$op=request('op','list',$ops);
if($op=='list'){
	$start = intval(request('start',0));
	$result=userlist(array('a.apps=\'seller\''),'`regtime` DESC',$start,30,'seller');
	$userlist=array();
	if (!empty($result))
	{
		$page_url=$_extend_url.'&pmod=user&'.$result['url'];
		$pages=get_page_number_list($result['total'], $start, 30);
		$userlist=$result['data'];
	}
}
elseif ($op=='add'){
	if(submitcheck('selleradd')){
		$user_field=request('user_field',array());
		//检测用户名名合法性
		if( !lib_validate::user_name($user_field['user_name']) )
		{
			show_message('系统提示','用户名格式不正确',-1);
		}
		if( !lib_validate::email($user_field['email']) )
		{
			show_message('系统提示','邮箱格式不正确',-1);
		}
		if(!empty($user_field['mobile'])){
			//检测手机名合法性
			if( !lib_validate::mobile($user_field['mobile']) )
			{
				show_message('系统提示','手机格式不正确',-1);
			}
			if(check_account_exist($user_field['mobile'],'mobile',$user_field['uid'],'seller')){
				show_message('系统提示','手机被占用',-1);
			}
		}
		$user_field['apps']='seller';
		$user_field['groups']='seller-seller';
		if(empty($user_field['uid'])){
			//判断密码
			if($user_field['reuserpwd']!=$user_field['userpwd'] || empty($user_field['userpwd'])){
				show_message('系统提示','确认密码错误！',-1);
			}
			unset($user_field['reuserpwd']);
			$user_field['regtime']=$_timestamp;
			try {
				register($user_field);
			}
			catch ( Exception $e )
			{
				$errmsg = $e->getMessage();
				show_message('系统提示',$errmsg,-1);
			}
		}else{
			//判断密码
			if($user_field['reuserpwd']!=$user_field['userpwd'] && (!empty($user_field['userpwd']) || !empty($user_field['reuserpwd']))){
				show_message('系统提示','确认密码错误！',-1);
			}else{
				unset($user_field['reuserpwd'],$user_field['userpwd']);
			}
			unset($user_field['reuserpwd']);
			try {
				up_seller_user($user_field);
			}catch ( Exception $e )
			{
				$errmsg = $e->getMessage();
				show_message('系统提示',$errmsg,-1);
			}
		}
		show_message('系统提示','用户添加/修改成功',$_extend_url.'&pmod=user');
	}
	$uid=request('uid',0);
	if(!empty($uid)){
		$seller=get_user_info('a.uid='.$uid,'seller');
	}
}
/* End of file user.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\admin\mod\seller\user.act.php */