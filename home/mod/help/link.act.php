<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$link_rule='';
if(!empty($_webset['link_rule'])){
	$link_rule=getarticle($_webset['link_rule']);
}
?>