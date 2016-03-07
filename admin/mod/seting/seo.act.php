<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$ops=array('set','page','add');
$op=request('op','set',$ops);
if($op=='set'){
	if(submitcheck('seoset')){
		$seo=request('seo',array());
		if(!empty($seo)){
			system::webset($seo);
			show_message('操作成功','设置成功','-1');
		}
	}
}elseif($op=='add'){
	if(submitcheck('pageadd')){
		$seo=request('seo',array());
		if(!empty($seo)){
			if(empty($seo['name'])){
				show_message('系统提示','请设置名称',-1);
			}
			if(empty($seo['mod'])){
				show_message('系统提示','请设置模型',-1);
			}
			if(empty($seo['ac'])){
				show_message('系统提示','请设置行为',-1);
			}
			system::seoset($seo);
			show_message('操作成功','关键词设置成功','?mod='.MODNAME.'&ac='.ACTNAME);
		}
	}else{
		$id=request('id');
		if(!empty($id)){
			$seo=$_seo[$id];
			$seo['tag']=explode('|',$seo['tag']);
			foreach ($seo['tag'] as $key=>$value){
				$seo['tag'][$key]=explode('-',$value);
			}
		}
	}
}
/* End of file seo.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\seting\seo.act.php */