<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

define('IS_EXTEND',true);
define('APPNAME','home');
system::_init();
system::check_purview();
register_shutdown_function('statistics');
define('PATH_TPL','./home/template/'.$_webset['site_tpl']);
define('INTEGRAL',empty($_webset['base_integralName'])?'积分':$_webset['base_integralName']);
define('DEF_GD_LOGO',empty($_webset['site_goodlogo'])?'static/images/default.gif':$_webset['site_goodlogo']);

include PATH_APP.'/lib/common.fun.php';
