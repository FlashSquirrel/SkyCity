<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
define('IS_EXTEND',true);
define('APPNAME','special');
system::_init();
system::check_purview();
include PATH_APP.'/lib/common.fun.php';
/* End of file common.inc.php */