<?php

if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$Shortcut = "[InternetShortcut]
URL={$_webset['site_url']}
IDList=
IconFile={$_webset['site_url']}/favicon.ico
IconIndex=1
[{000214A0-0000-0000-C000-000000000046}]
Prop3=19,2
";
Header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename={$_webset['site_name']}.url;"); 
echo $Shortcut;
exit();
?>