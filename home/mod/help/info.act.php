<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$id=request('id');
$cid=request('cid');
if(empty($id) && !empty($cid)){
	$helparticle=gethelp();
	reset($helparticle[$cid]);
	$article=current($helparticle[$cid]);
}else{
	$article=getarticle($id);
}
if(empty($article)){
	message('-1','操作错误','文章不存在',u(MODNAME,'index'));
}else{
	//文章简单内容
	$article['summary']=utf8_substr(str_replace(array("\n","\t","\s"),"",strip_tags($article['content'])),100);
}
?>