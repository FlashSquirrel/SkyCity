<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

define('IS_EXTEND',true);
define('APPNAME','home');
system::_init();
system::check_purview();
register_shutdown_function('statistics');
define('PATH_TPL','./home/template/'.$_webset['site_tpl']);
include PATH_APP.'/lib/common.fun.php';

define('INTEGRAL',empty($_webset['base_integralName'])?'积分':$_webset['base_integralName']);
define('PAGE',empty($_webset['base_goodspagenum'])?30:$_webset['base_goodspagenum']);
define('CPAGE',empty($_webset['base_commentpagenum'])?20:$_webset['base_commentpagenum']);
define('GIFT_LOGO',empty($_webset['gift_logo'])?PATH_APP.'/static/images/logo.png':$_webset['gift_logo']);
define('GIFT_SUB_LOGO',empty($_webset['gift_sub_logo'])?PATH_APP.'/static/images/sub_logo.png':$_webset['gift_sub_logo']);
define('DEF_GD_LOGO',empty($_webset['site_goodlogo'])?'static/images/default.gif':$_webset['site_goodlogo']);
/* End of file common.inc.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\gift\common.inc.php */