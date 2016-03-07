<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @clear.act.php
 * =================================================
*/
$start=request('start',0);
function clear_cache($dir,$del=false) {
	showjsmessage('清空目录 '.str_replace(PATH_DATA, '',$dir));
	//先删除目录下的文件：
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				clear_cache($fullpath,true);
			}
		}
	}
	closedir($dh);
	//删除当前文件夹：
	if($del && rmdir($dir)) {
		return true;
	} else {
		return false;
	}
}
function showjsmessage($message) {
	echo '<script type="text/javascript">show_message(\''.addslashes($message).' \');</script>'."\r\n";
}
/* End of file clear.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\admin\mod\data\clear.act.php */