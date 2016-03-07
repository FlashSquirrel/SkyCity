<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$op=request('op');
$api=request('api');
if(empty($api)){
	message('-1','登陆提示','快捷登陆操作错误',u('user','login'));
}
require PATH_API.'/fastlogin.php';
$connect=new fastlogin($api);
if($op=='callback'){
	$data=$connect->callback();
	//用户快捷登陆操作
	falselogin($data);
	header('location:'.u(MODNAME,'index'));
}else{
	$connect->set_callback(u(MODNAME,'fastlogin',array('op'=>'callback','api'=>$api),true));
	$login=$connect->login();
	//登陆地址
	header('location:'.$login);
}
exit();
/* End of file fastlogin.act.php */