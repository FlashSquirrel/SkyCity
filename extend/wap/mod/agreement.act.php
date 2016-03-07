<?php if(!defined('PATH_ROOT')){exit('Access Denied');}

$article='';
if(!empty($_webset['base_agreement'])){
	$article=getarticle($_webset['base_agreement']);
	$article=$article['content'];
}
require tpl_extend(WAP_TPL.'/agreement.tpl.php');
/* End of file agreement.act.php */
/* Location: E:\wwwroot\taobaoke\xuanyu\extend\wap\mod\agreement.act.php */