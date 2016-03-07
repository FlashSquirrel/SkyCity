<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>天城管理中心</title>
<script src="../static/js/jquery-1.10.2.min.js"></script>
<script src="static/js/artDialog.js"></script>
<link rel="stylesheet" type="text/css" href="static/css/common.css" />
<link href="static/css/dialog.css" rel="stylesheet" type="text/css" />
</head>
<body style="overflow:hidden;">
	<div class="top">
		<div class="side_switch" onclick="side_menu();"></div>
		<div class="admin_logo">
			<img src="static/images/admin_logo.png">
		</div>
		<div class="top_nav">
			<ul>
				<?php foreach ($menu as $k=>$v){ ?>
				<li><a href="javascript:void(0);" data-id="<?=$v['id'];?>" <?php if($k==1){?>class="selected"<?php } ?> onclick="show_sub_menu('<?=$v['id'];?>');">
					<?=$v['name'];?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="left">
		<div class="member_info">
			<a class="system_a" href="javascript:void(0);"><?=$user['user_name'];?></a>
			<a class="system_b" href="?mod=seting&ac=manager&op=listadd&uid=<?=$user['uid'];?>" target="main">密码</a>
			<a class="system_c" href="?mod=index&ac=loginout">退出</a>
		</div>
		<div id="menu">
			<?php foreach ($sub_menu[1] as $key=>$value){?>
			<div class="left_title" onclick="show_menu($(this).next())"><?=$value['name'];?></div>
			<ul class="side">
				<?php foreach ($value['item'] as $val){ ?>
				<li><a href="<?=$val['url'];?>" target="main" onclick="choose_item($(this))"><?=$val['name'];?></a></li>
				<?php } ?>
			</ul>
			<?php } ?>
		</div>
		<ul class="side catsub">
			<li class="side_web"><a href="../" target="_blank">网站前台</a></li>
		</ul>
	</div>
	<div class="right">
		<div class="top_subnav">天城管理中心</div>
		<p class="line mt5"></p>
		<iframe name="main" src="?ct=index&ac=welcome" frameBorder="0" width="100%" scrolling="no" style="height: 100%;min-width:1020px"></iframe>
	</div>
</body>
</html>
<script type="text/javascript">
var sub_menu=<?=json_encode($sub_menu);?>;
var dataid=0;
$(function(){
	dataid=$(".selected").attr("data-id");
	//敞口大小调整
	resizable();
	$(window).resize(function(){
		resizable();
	})
	//菜单悬停
	$(".top_nav a").mouseenter(function(){
		//dataid=$(".selected").attr("data-id");
		$(".top_nav a").removeClass("selected");
		$(this).addClass("selected");
	})
	//鼠标离开
	$(".top_nav ul").mouseleave(function(){
		$(".top_nav a").removeClass("selected");
		$(".top_nav a[data-id='"+dataid+"']").addClass("selected");
	});
	load_menu();
})
//调整视窗大小
function resizable(){
	var height=$(window).height()-72;
	$(".left").height(height);
	//IE是
	if(window.attachEvent){ 
           $(".right").height(height-5).find("iframe").height(height-30);
    }else{
    	$(".right").height(height-45).find("iframe").height(height-45);
    }
}
function show_sub_menu(id){
	dataid=id;
	var html='';
	for(var i in sub_menu[id]){
		html+='<div class="left_title" onclick="show_menu($(this).next())">'+sub_menu[id][i].name+'</div>';
		var item=sub_menu[id][i].item;
		html+='<ul class="side">';
		for(var j in item){
			html+='<li><a href="'+item[j].url+'" target="main" onclick="choose_item($(this))">'+item[j].name+'</a></li>';
		}
		html+='</ul>';
	}
	$("#menu").html(html);
	load_menu();
}
function show_menu(this_menu){
	if(this_menu.is(":hidden")){
		this_menu.slideDown(500);
	}else{
		this_menu.slideUp(500);
	}
}
function load_menu(){
	//展开然后过几秒合并
	setTimeout(function(){
		$("#menu .side").not($("#menu .side").eq(0)).slideUp(500);
	},500);
}
function choose_item(obj){
	var main_menu=obj.parents("ul").prev().html();
	var item_menu=obj.html();
	$(".top_subnav").html('天城管理中心&nbsp;&gt;&nbsp;'+main_menu+'&nbsp;&gt;&nbsp;'+item_menu);
}
//左侧菜单
function side_menu(){
	//查看当前left值()
	var isshow=parseInt($(".left").css("left"));
	if(!isshow){
		$(".left").animate({"left":'-205px'},500);
		$(".right").animate({"margin-left":'20px'},500);
		$(".side_switch").animate({"left":'0px'},{duration:500}).css({"background-image":"url('static/images/close_side_icol.jpg')"});
	}else{
		$(".left").animate({"left":'0px'},500);
		$(".right").animate({"margin-left":'210px'},500);
		$(".side_switch").animate({"left":'197px'},{duration:500}).css({"background-image":"url('static/images/close_side_ico.jpg')"});;
	}
}
//版权声明
function copyright(){
	art.dialog({
		id:'msgDialog',
		title:"版权声明",
		lock:true,
		fixed:true,
		width:500,
		height:200,
		content:'<div style="line-height: 25px;"></div>'
	});	
}
</script>