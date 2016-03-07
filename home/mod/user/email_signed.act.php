<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
//注册邮件验证
$email=request('email','');
if(!empty($email)){
	preg_match('/.*?\@(.*+)/',$email,$domain);
	$maildomain='http://mail.'.$domain[1];
	//判断类型
	$activatinglog=activatinglog($email);
	if(empty($activatinglog)){
		message(0,'系统提示','操作错误',-1);
	}
}

?>