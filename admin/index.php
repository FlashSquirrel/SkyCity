<?php

define('APPNAME','admin');
define('PATH_APP',getcwd());
require '../library/init.php';
require PATH_APP.'/common.inc.php';
//调用
require execute_mod(MODNAME,ACTNAME);
require tpl_mod(MODNAME,ACTNAME);
?>