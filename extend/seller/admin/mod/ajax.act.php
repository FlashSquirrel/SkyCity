<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

require PATH_EXTEND.'/lib/common.fun.php';
$op=request('op');
$jsonp_callback=request('callback','');
switch ($op){
	case 'recharge_audit':
		$money=request('money',0);
		$trade_no=request('trade_no','');
		$lid=request('lid',0);
		if(empty($lid) || empty($trade_no) || floatval($money)==0){
			echo $jsonp_callback.'({"code":-1,"msg":"操作失败"})';
			exit();
		}
		$changelog=check_trade_no($lid,$trade_no);
		if(empty($changelog)){
			echo $jsonp_callback.'({"code":-1,"msg":"操作失败"})';
			exit();
		}
		recharge_audit($trade_no,$money,$changelog['uid']);
		echo $jsonp_callback.'({"trade_no":"'.$trade_no.'","code":0,"msg":"审核成功"})';
		break;
	case 'withdraw_audit':
		$money=request('money',0);
		$serialno=request('serialno','');
		$trade_no=request('trade_no','');
		if(empty($trade_no) || empty($serialno) || floatval($money)>0){
			echo $jsonp_callback.'({"code":-1,"msg":"操作失败"})';
			exit();
		}
		$withdrawlog=check_serialno($serialno);
		if(empty($withdrawlog) || $withdrawlog['money']!=$money){
			echo $jsonp_callback.'({"code":-1,"msg":"操作失败"})';
			exit();
		}
		withdraw_audit($serialno,$trade_no,$withdrawlog['money'],$withdrawlog['uid']);
		echo $jsonp_callback.'({"serialno":"'.$serialno.'","trade_no":"'.$trade_no.'","code":0,"msg":"审核成功"})';
		break;
	default:
		echo $jsonp_callback.'({"code":-1,"msg":"操作失败"})';
}
exit();
/* End of file ajax.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\seller\admin\mod\ajax.act.php */