<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>天城管理系统</title>
<script src="../static/js/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../static/js/common.js" type="text/javascript"></script>
<link href="static/css/login.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="static/css/reset.css" />
<link type="text/css" rel="stylesheet" href="static/css/theme.css" />
<link type="text/css" rel="stylesheet" href="static/css/global.css" />
<script>
	$(function(){
	var _select=$('.select');
		_select.click(function(){
			$(this).find('ul').show();
		})

		$('.select li').click(function(){
			var eid=$(this).attr('eid');
			var eid_input='<input type="hidden" value="'+eid+'" name="eid" id="eid" \/>';
			var _eidhtml=$(this).html();
			$('.eid_value').html(_eidhtml);
			if($('#eid').attr('value')){
				$('#eid').attr('value',eid);	
			}
		})
		$('.select ul').hover(function(){
			
		},function(){
			$(this).hide();
		})
	})
</script>
</head>
<body>
<div class="sky"></div>

<div class="sun">
	<div class="kernel"></div>
	<div class="shine"></div>
</div>

<div class="sea">
	<div class="bubble bubble1"></div>
	<div class="bubble bubble2"></div>
	<div class="bubble bubble3"></div>
	<div class="bubble bubble4"></div>
	<div class="bubble bubble5"></div>
	<div class="bubble bubble6"></div>
	<div class="bubble bubble7"></div>
	<div class="jellyfish jellyfish1">
		<div class="jellyfish_head"></div>
		<div class="jellyfish_tail">
			<div class="jellyfish_tail_in"></div>
		</div>
	</div>
	<div class="jellyfish jellyfish2">
		<div class="jellyfish_head"></div>
		<div class="jellyfish_tail">
			<div class="jellyfish_tail_in"></div>
		</div>
	</div>
</div>
<div class="page">
	<div class="main">
		<div class="content">
		<div class="login">
		<div class="login_form">
			<div class="login_info">
				<div class="login_info_title">
					<div class="fl" id="login_set"></div>
					<?=system::admin_login();?>
				</div>
			</div>
			<form action="?mod=<?=MODNAME;?>&ac=login" method="post" >
			<div class="form_info">
				<div class="field">
					<label>用户名：</label>
					<input type="text" name="username" class="text" size="20">
				</div>
				<div class="field">
					<label>密　码：</label>
					<input type="password" name="password" class="text" size="20">
				</div>
				<div class="field">
					<label>验证码：</label>
					<input type="text" name="validate" class="text" size="8">
                    <img id="vdimgck" src="../?mod=index&ac=validate_image&h=28&w=68&f=14" alt="看不清？点击更换" align="absmiddle" style="cursor:pointer;margin-top: -5px;" onclick="this.src=this.src+'?'" />
				</div>
				<div class="field">
					<label></label>
					<button type="submit" class="button" style="margin-left:50px;_margin-left:48px"></button>
				</div>
				<?php if(!empty($errmsg)){ ?>
				<div  id="message"><?=$errmsg;?></div>
				<?php } ?>
			</div>
			</form>
		</div>
	</div>
		</div>
	</div>
</div>

	
</body>
</html>