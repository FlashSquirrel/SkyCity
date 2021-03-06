<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

define('APPNAME','home');
system::_init();
system::check_purview();
register_shutdown_function('statistics');
//模板
if(!defined('PATH_TPL')){
	$_webset['site_tpl']=empty($_webset['site_tpl'])?'default':$_webset['site_tpl'];
	define('PATH_TPL','./home/template/'.$_webset['site_tpl']);
}
//设置常量
define('PAGE',empty($_webset['base_goodspagenum'])?30:$_webset['base_goodspagenum']);
define('CPAGE',empty($_webset['base_commentpagenum'])?20:$_webset['base_commentpagenum']);
define('INTEGRAL',empty($_webset['base_integralName'])?'积分':$_webset['base_integralName']);
define('LOGO',empty($_webset['site_logo'])?'static/images/logo.png':$_webset['site_logo']);
define('DEF_GD_LOGO',empty($_webset['site_goodlogo'])?'static/images/default.gif':$_webset['site_goodlogo']);
//二维码
$_webset['site_weixinpic']=empty($_webset['site_weixinpic'])?'static/images/code.png':$_webset['site_weixinpic'];
//判断是否手机访问
if(!empty($_webset['wap_status'])){
	$ismobile=false;
	$form_wap=get_cookie('form_wap');
	if(empty($form_wap)){
		$form_wap=request('form_wap',0);
	}
	if(!empty($form_wap)){
		_put_cookie('form_wap',1,0);
		$ismobile=false;
	}else{
		$ismobile=true;
	}
	if(!empty($_webset['wap_browse']) && $ismobile && isMobile() && MODNAME!='wap' && MODNAME!='ajax' && ACTNAME!='validate_image'){
		header("location:".u('wap','index'));
	}
}
//自动删除
if($_webset['base_autoDel']==1){
	deloverGoods();
}
include PATH_APP.'/lib/hook.fun.php';
