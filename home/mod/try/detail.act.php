<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$id=intval(request('id',0));
if(empty($id)){
	message('-1','提示',"试用不存在",u('try','index'));
}

$try=tryinfo($id);
if(empty($try)){
	message('-1','提示',"试用不存在",u('try','index'));
}

//热门试用
function hottry($id){
	return lib_database::select('try','*','id!=\''.$id.'\' order by apply desc limit 0,4');
}
//谁获取了试用
function whogettry($id,$num=20){
	$query = lib_database::rquery('select uid,user_name from '.tname('applylog').' where id=\''.$id.'\' and status>1 and idtype=\'try\' order by addtime desc limit 0,'.$num);
	while ($rt = lib_database::fetch_one())
	{
		$data[] = $rt;
	}
	return $data;
}
?>