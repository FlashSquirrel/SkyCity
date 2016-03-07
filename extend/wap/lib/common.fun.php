<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

function get_wap_tpl(){
	//读取模板
	$tplarr=getDir(PATH_EXTEND.'/template/');
	foreach ($tplarr as $k=>$val){
		$tpl[$val]=$val;
	}
	return $tpl;
}
/* End of file common.fun.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\lib\common.fun.php */