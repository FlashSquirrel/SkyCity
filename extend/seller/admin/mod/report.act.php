<?php if(!defined('PATH_ROOT')){exit('Access Denied');}
$ops=array('set','list');
$op=request('op','set',$ops);
if($op=='set'){
	if(submitcheck('repotset')){
		$repot=request('repot',array());
		$repot['report_login']=intval($repot['report_login']);
		system::webset($repot);
		show_message('操作成功','设置成功',$_extend_url.'&pmod=report');
	}
}else{
	$start = intval(request('start',0));
	$result=reportlist(array(),'`addtime` DESC',$start,30);
	$reportlist=array();
	if (!empty($result))
	{
		$page_url=$_extend_url.'&pmod=report&op=list&'.$result['url'];
		$pages=get_page_number_list($result['total'], $start, 30);
		$reportlist=$result['data'];
	}
}
/* End of file report.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\admin\mod\seller\report.act.php */