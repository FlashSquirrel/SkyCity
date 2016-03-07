<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$ops=array('list','listadd');
$op=request('op','list',$ops);


if($op=='list'){
	$start = intval(request('start',0));
	$result=managerlist(array('apps=\'admin\''),'`regtime` desc',$start,30);
	$managelist=array();
	if (!empty($result))
	{
		$page_url='?mod='.MODNAME.'&ac='.ACTNAME;
		$pages=get_page_number_list($result['total'], $start, 30);
		$managelist=$result['data'];
	}
}
elseif ($op=='listadd'){
	$manager=request('manager',array());
	//添加
	if(!empty($manager)){
		$manager['apps']='admin';
		$manager['groups']='admin-admin';
		//用户名验证
		if(!empty($manager['uid'])){
			if(!lib_validate::user_name($manager['user_name'])){
				show_message('操作失败','用户名格式错误','-1');
			}
			if(check_account_exist($manager['user_name'],'user_name',$manager['uid'],'admin')){
				show_message('操作失败','用户名被占用','-1');
			}
			//验证邮箱是否被占用
			if(!lib_validate::email($manager['email'])){
				show_message('操作失败','邮箱格式错误','-1');
			}
			if(check_account_exist($manager['email'],'email',$manager['uid'],'admin')){
				show_message('操作失败','邮箱被占用','-1');
			}
		}else{
			if(!lib_validate::user_name($manager['user_name'])){
				show_message('操作失败','用户名格式错误','-1');
			}
			if(check_account_exist($manager['user_name'],'user_name',0,'admin')){
				show_message('操作失败','用户名被占用','-1');
			}
			//验证邮箱是否被占用
			if(!lib_validate::email($manager['email'])){
				show_message('操作失败','邮箱格式错误','-1');
			}
			if(check_account_exist($manager['email'],'email',0,'admin')){
				show_message('操作失败','邮箱被占用','-1');
			}
		}
		//判断 确认密码
		if(empty($manager['userpwd']) || $manager['userpwd']!==$manager['reuserpwd']){
			show_message('操作失败','确认密码错误','-1');
		}
		try {
			unset($manager['reuserpwd']);
			manager($manager);
			show_message('操作成功','管理员设置成功','?mod='.MODNAME.'&ac='.ACTNAME);
		}
		catch ( Exception $e )
		{
			//错误提示
			$errmsg = $e->getMessage();
			show_message('操作失败',$errmsg,'-1');
		}
	}else{
		$uid=request('uid');
		if(!empty($uid)){
			if($uid!=$user['uid'] && $user['apps']!=='admin'){
				show_message('操作失败','您没有权限','?mod='.MODNAME.'&ac='.ACTNAME);
			}
			$manager=getmanager($uid);
		}elseif($user['apps']!='admin'){
			show_message('操作失败','您没有权限','?mod='.MODNAME.'&ac='.ACTNAME);
		}
	}
}

?>