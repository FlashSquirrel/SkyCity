<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$op=request('op');
$id=request('id');
$idstr=(!empty($id) && is_array($id))?implode(',',$id):'';
if(empty($idstr)){
	show_message('操作成功','数据删除成功',-1);
}
//返回跳转
$gomod=request('gomod');
$goop=request('goop');
switch ($op){
	case 'goods'://删除活动
		//删除图片
		lib_database::rquery('select pic from '.tname('goods').' where id in ('.$idstr.')');
		while ($value=lib_database::fetch_one()){
			if(!empty($value['pic']) && check_img($value['pic'])){
				@unlink(PATH_ROOT.$value['pic']);
				@unlink(PATH_ROOT.$value['pic'].'_290x190.jpg');
			}
		}
		lib_database::wquery('delete a,b from '.tname('goods').' as a left join '.tname('urls').' as b on a.num_iid=b.iid where a.id in('.$idstr.')');
		$gourl='&pmod='.$gomod.'&op='.$goop;
		break;
	case 'special'://删除专场
		lib_database::wquery('delete from '.tname('special').' where sid in('.$idstr.')');
		lib_database::wquery('delete from '.tname('special_position').' where sid in('.$idstr.')');
		$gourl='&pmod='.$gomod.'&op='.$goop;
		break;
		
}
show_message('操作成功','数据删除成功',$_extend_url.$gourl);
/* End of file del.act.php */