<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$bid=request('bid',0);
if(empty($bid)){
	show_message('系统提示','操作错误',-1);
}
$nid=brandNid();
$brand=getbrand($bid);
lib_database::rquery('select * from '.tname('goods').' where channel='.$nid.' and cat=\''.$bid.'\' order by `sort` desc limit 0,50');
while ($rt = lib_database::fetch_one())
{
	$goodslist[] = $rt;
}
?>