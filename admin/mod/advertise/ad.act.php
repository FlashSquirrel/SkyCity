<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
$ops=array('adList','adAdd');
$op=request('op','adList',$ops);
if ($op=='adAdd'){
	if(submitcheck('addad')){
		$ad=request('ad',array());
		if(!empty($ad)){
			$ad['start']=strtotime($ad['start']);
			$ad['end']=strtotime($ad['end']);
			system::adset($ad);
			show_message('广告管理','广告添加成功','?mod='.MODNAME.'&ac='.ACTNAME);
		}
	}else{
		$id=request('id');
		if(!empty($id)){
			$ad=$_ad['ad_0'][$id];
		}
	}
}
/* End of file ad.act.php */
/* Location: D:\website\taobaoke\jiu2\admin\mod\advertise\ad.act.php */