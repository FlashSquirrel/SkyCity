<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$op=request('op');
$jsonp_callback=request('callback','');

switch ($op){
	case 'status':
		break;
	case 'gather':
		gather();
		break;
	case 'brandgather':
		brandgather();
		break;
	//评论
	case 'comment':
		comment();
		break;
	//收藏
	case 'goodsfav':
		goodsfav();
		break;
	//图片上传
	case 'ajaxfile':
		ajaxfile();
		break;
	//编辑器图片上传
	case 'editorUpload':
		editorUpload();
		break;
	//晒单上传
	case 'ajaxsunpic':
		ajaxsunpic();
		break;
	//删除晒单图片
	case 'delsunpic':
		delsunpic();
		break;
	//设置头像
	case 'setavatar':
		setavatar();
		break;
	case 'delfav':
		delfav();
		break;
	case 'delgiftlog':
		delgiftlog();
		break;
	//举报
	case 'report':
		report();
		break;
	//抢光了
	case 'over':
		goodsover();
		break;
	default:
		echo $jsonp_callback.'({"code":-1,"msg":"操作失败"})';
}
exit();

?>