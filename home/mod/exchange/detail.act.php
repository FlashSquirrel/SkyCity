<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}

$id=intval(request('id',0));
if(empty($id)){
	message('-1','提示',"兑换不存在",u('exchange','index'));
}

$exchange=getexc($id);
if(empty($exchange)){
	message('-1','提示',"兑换不存在",u('exchange','index'));
}
?>