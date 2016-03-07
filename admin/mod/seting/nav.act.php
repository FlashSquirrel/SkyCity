<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
$ops=array('navlist','navadd');
$op=request('op','navlist',$ops);
//导航列表
if($op=='navlist'){
	
}
elseif ($op=='navadd'){
	if(submitcheck('navadd')){
		$nav=request('nav',array());
		if(!empty($nav)){
			if(!empty($nav['url'])){
				if(empty($nav['name'])){
					show_message('操作失败','请填写导航名称',-1);
				}
			}elseif(empty($nav['name']) || empty($nav['mod']) || empty($nav['ac']) || empty($nav['tag'])){
				show_message('操作失败','导航名，模型，行为，短标示不能为空','-1');
			}
			system::navset($nav);
			show_message('操作成功','导航添加成功','?mod='.MODNAME.'&ac='.ACTNAME);
		}
	}else{
		$id=request('id');
		$nav=$_nav[$id];
	}
}
/* End of file nav.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\seting\nav.act.php */