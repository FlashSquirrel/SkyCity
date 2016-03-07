<?php
if(!defined('PATH_ROOT')){
	exit('Access Denied');
}
$validate_image_config  =  array(
	'font_size'=>intval(request('f',18)),
	'img_height'=>intval(request('h',36)),
	'img_width'=>intval(request('w',100)),
	'use_boder'=>true,
	'filter_type'=>intval(request('t', 0))
);
if($validate_image_config['font_size']>30){
	$validate_image_config['font_size']=18;
}
if($validate_image_config['img_height']>50){
	$validate_image_config['img_height']=36;
}
if($validate_image_config['img_width']>200){
	$validate_image_config['img_width']=100;
}
echo_validate_image( $validate_image_config );

?>