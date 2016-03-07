<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

//删除处理
$type=request('type','',array('goods','album','try','exchange'));
$id=request('id',0);
if(empty($type) || empty($id)){
	extend_message(0,'系统提示','操作错误！');
}
if($type=='goods' || $type=='album'){
	del_goods($id);
	extend_message(0,'系统提示','删除成功！',u(MODNAME,'manage',array('op'=>$type)));
}
//删除试用
elseif ($type=='try'){
	del_try($id);
	extend_message(0,'系统提示','删除成功！',u(MODNAME,'manage',array('op'=>$type)));
}
elseif ($type=='exchange'){
	del_exc($id);
	extend_message(0,'系统提示','删除成功！',u(MODNAME,'manage',array('op'=>$type)));
}

/* End of file del.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\seller\mod\del.act.php */