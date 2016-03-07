<?php
if (ini_get('session.auto_start')){
    exit('php.ini session.auto_start must is off ! ');
}
date_default_timezone_set('Asia/Shanghai');
$GLOBALS['_timestamp'] = time();
define('PATH_ROOT', substr(dirname(__FILE__), 0, -7));
define('PATH_LIBRARY', PATH_ROOT . 'library');
define('PATH_CONFIG', PATH_ROOT . 'config');
define('PATH_API', PATH_ROOT . 'api');
define('PATH_DATA', PATH_ROOT . 'data');
define('PATH_UPLOAD', './upload');
define('FILTER_WORD', PATH_DATA . '/filterwords.inc.php');
require PATH_CONFIG . '/inc_config.php';
header('Content-Type: text/html; charset=' . $GLOBALS['config']['charset']);
session_save_path(PATH_DATA . '/session');
if(DEBUG_LEVEL == true){
    ini_set('display_errors', 'On');
    error_reporting(6143);
}else{
    ini_set('display_errors', 'Off');
    error_reporting(0);
}
require PATH_LIBRARY . '/comm/lib_debug.php';
set_exception_handler('debug_exception_handler');
set_error_handler('debug_error_handler', E_ALL);
register_shutdown_function('debug_error_show');
require PATH_LIBRARY . '/comm/lib_request.php';
lib_request :: init();
require PATH_LIBRARY . '/comm/lib_database.php';
require PATH_LIBRARY . '/comm/lib_access.php';
require PATH_LIBRARY . '/func/fun_common.php';
require PATH_LIBRARY . '/func/fun_operating.php';
require PATH_LIBRARY . '/func/fun_view.php';
require PATH_LIBRARY . '/system/system.php';
if(!defined('APPNAME')){
    system :: check_sun_domain();
}
if(!defined('MODNAME')){
    define('MODNAME', preg_replace("/[^0-9a-z_]/i", '', request('mod', 'index')));
}
if(!defined('ACTNAME')){
    define('ACTNAME', preg_replace("/[^0-9a-z_]/i", '', request('ac', 'index')));
}
$_isajax = request('inajax', false);
$GLOBALS['_ip'] = @get_client_ip();
require PATH_CONFIG . '/set_config.php';
?>