<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
	define('COOKIE_DOMAIN', 'localhost'); //正式环境中如果要考虑二级域名问题的应该用 .xxx.com
	define('COOKIE_PATH', '/');
	define('URL', 'http://localhost/tiancheng/tiancheng');
// --------------------------  CONFIG CONFIG  --------------------------- //
$GLOBALS['config']['db_host']['master'] = 'localhost';
$GLOBALS['config']['db_host']['slave']['0'] = 'localhost';
$GLOBALS['config']['db_user'] = 'root';
$GLOBALS['config']['db_name'] = 'tiancheng';
$GLOBALS['config']['db_pass'] = 'root';
$GLOBALS['config']['db_pre'] = 'pre_';
$GLOBALS['config']['cookie_pre'] = '1MPXxrH#H9bL';
$GLOBALS['config']['cookie_pwd'] = 'VtdmDKE#JAk3';


// -------------------  THE END  -------------------- //

?>