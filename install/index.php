<?php

define('PATH_APP',getcwd());
define('APPNAME','install');
define('PATH_TPL','./template');
require '../library/init.php';
require './lib/comm/var.php';
require './lib/func/common.func.php';
system::check();
require execute_mod('',ACTNAME);
require tpl_mod('',ACTNAME);
?>