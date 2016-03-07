<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

if(submitcheck('baseset')){
	$base=request('base',array());
	$base['extend_seller_auditrecharge']=intval($base['extend_seller_auditrecharge']);
	$base['extend_seller_apirecharge']=intval($base['extend_seller_apirecharge']);
	if(empty($base['extend_seller_auditrecharge']) && empty($base['extend_seller_apirecharge'])){
		show_message('操作成功','审核充值和支付宝充值请至少启用一种','-1');
	}
	system::webset($base,true);
	show_message('操作成功','设置成功','-1');
}
/* End of file baseset.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\admin\mod\merchant\baseset.act.php */