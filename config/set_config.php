<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

if(defined('V_MODE')){
	system::show_check();
}
define('V_MODE','vip');
if(MODNAME=='wap' || MODNAME=='seller' || MODNAME=='album' || MODNAME=='gift'){
	if(!defined('PATH_APP'))define('PATH_APP','./extend/'.MODNAME);
}elseif (MODNAME=='qzone' || MODNAME=='screenad'){
	if(!defined('PATH_APP'))define('PATH_APP','./plugin/'.MODNAME);
}else{
	if(!defined('PATH_APP'))define('PATH_APP','./home');
}
$_seller_mod='business';
$_exc_mod='exchange';
$_hash='3b6464abca65b58f8fed42a0a7191b51';
if(V_MODE=='vip'){
	$_seller_mod='seller';
	$_exc_mod='gift';
}
