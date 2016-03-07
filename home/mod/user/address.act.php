<?php

if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
//保存地址
$address=request('address');
if(!empty($address)){
	$address['uid']=$user['uid'];
	//用户名为空
	if(empty($address['truename'])){
		message('-1','提示','真实姓名不能为空',-1);
	}
	if($address['province']=='省份' || $address['city']=='地级市' || $address['county']=='市、县级市' || empty($address['province']) || empty($address['city']) || empty($address['county'])){
		message('-1','提示','请选择地区',-1);
	}
	if(empty($address['addr'])){
		message('-1','提示','请填写正确的收货地址',-1);
	}
	if(empty($address['mobile']) || (!lib_validate::mobile($address['mobile']) && !lib_validate::phone($address['mobile']))){
		message('-1','提示','请填写正确的电话号码',-1);
	}
	if(empty($address['postcode']) || !lib_validate::zip($address['postcode'])){
		message('-1','提示','请填写正确的邮编',-1);
	}
	saveaddress($address);
	message('0','提示','地址保存成功',u('user','address'),$address);
}
$address=useraddress($user['uid']);
?>