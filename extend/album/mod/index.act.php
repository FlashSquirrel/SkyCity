<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$aid=request('aid',0);
if(empty($aid)){
	header('location:'.u('index','index'));
	exit();
}
//专题信息
$album=get_album_info($aid);
//专题模板
define('ALBUM_TPL',empty($album['tpl'])?'default':$album['tpl']);
define('ALBUM_TPL_PATH',PATH_APP.'/template/'.ALBUM_TPL);
//seo信息
$_seo[MODNAME.'_'.ACTNAME]['title']=empty($album['album_title'])?$album['title']:$album['album_title'];
$_seo[MODNAME.'_'.ACTNAME]['keyword']=empty($album['album_metakeyword'])?$album['title']:$album['album_metakeyword'];
$_seo[MODNAME.'_'.ACTNAME]['desc']=empty($album['album_metadescrip'])?$album['title']:$album['album_metadescrip'];
//专题宝贝
$album_goods=get_album_goods($aid);
require tpl_extend(ALBUM_TPL.'/index.tpl.php');
/* End of file index.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\album\mod\index.act.php */