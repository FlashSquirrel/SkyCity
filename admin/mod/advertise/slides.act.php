<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
/**
 * =================================================
 * @package		D:\website\taobaoke\jiu2\admin\mod\advertise\slides.act.php
 * @author		bank
 * @slides.act.php
 * =================================================
*/
$ops=array('list','sAdd');
$op=request('op','list',$ops);

if($op=='sAdd'){
	if(submitcheck('slidesAdd')){
		$slides=request('slides',array());
		if(!empty($slides)){
			$slides['start']=strtotime($slides['start']);
			$slides['end']=strtotime($slides['end']);
			$slides['type']=1;
			system::adset($slides);
			show_message('首页幻灯','广告添加成功','?mod='.MODNAME.'&ac='.ACTNAME);
		}
	}else{
		$id=request('id');
		if(!empty($id)){
			$slides=$_ad['ad_1'][$id];
		}
	}
}
/* End of file slides.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\advertise\slides.act.php */