<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="">
<head>
<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
<title>天城家居商品购买</title>
<meta name="keywords" content="网站关键词" />
<meta name="description" content="" />
<base href="http://202.200.48.93/" />
<link href="./home/template/default/static/css/common.css" type="text/css" rel="stylesheet"/>
<link href="./extend/gift/static/css/common.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript" src="static/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="static/js/common.js"></script>
<script type="text/javascript" src="./extend/gift/static/js/common.js"></script>
</head>
<body>
<div class="toolbar">
	<div class="toolbar_c area">
    	<div class="fr flow d5">
        
	        	<a href="#" class="contractkf" target="_blank">联系客服</a>
            |
                        <a href="http://202.200.48.93/?mod=seller" target="_blank">卖家中心</a>
        </div>	
        <div class="login_type fr d5">
        	<em>
            <!--登录前-
        	                <span class="fl">您好，</span>
				<div class="deopdown fl">
										<a href="http://202.200.48.93/?mod=user&ac=center" class="userNike">123</a>
					                    <i class="icon_arrow arrow_down"></i>
                    <ul class="dropdown_menu hidden">
                        <li><a href="http://202.200.48.93/?mod=user&ac=base">账号信息</a></li>
                        <li><a href="http://202.200.48.93/?mod=user&ac=invite">邀请好友</a></li>
                        <li><a href="http://202.200.48.93/?mod=user&ac=fav">我的收藏</a></li>
                        <li><a href="http://202.200.48.93/?mod=user&ac=address">收货地址</a></li>
                        <li><a href="http://202.200.48.93/?mod=user&ac=gift">我的礼品</a></li>
                        <li><a href="http://202.200.48.93/?mod=user&ac=logout">退出</a></li>
                    </ul>
                </div>|
                <span>您当前积分为：</span>
                <a href="http://202.200.48.93/?mod=user&ac=center" class="userNike">30</a>|
                <a href="http://202.200.48.93/?mod=user&ac=invite" class="msg" target="_blank">邀请好友</a>|
              
            </em>
            <div class="hidden"></div>
        </div>		
            	
        <div class="fl d5">
        	<a href="http://202.200.48.93/" target="_blank">天城家居</a>
          <!--  |
            <a href="http://202.200.48.93/?mod=wap" target="_blank">手机版</a>
                        -->
            |
            <a href="http://202.200.48.93/?mod=gift" target="_blank">积分商城</a>
                    </div>
    </div>
</div>
<div class="clear"></div>
<script type="text/javascript">
//签到选项卡
$(".deopdown").mouseenter(function(){$(this).find(".dropdown_menu").show();})
$(".deopdown").mouseleave(function(){$(this).find(".dropdown_menu").hide();})
</script><div class="header">
	<div class="area">
    	<h1 class="fl">
        	<a href="http://202.200.48.93/">
            	<img src="./static/images/logo.png" width="300" height="60px"/>
            </a>
        </h1>
        <div class="links fr" style="margin-right:0px;background: url('./extend/gift/static/images/sub_logo.png') no-repeat;"></div>
    </div>
</div>
<div class="head_nav">
	<div class="head_nav_c area">
    	<div class="fl nav_list">
    			    			    			        	<a class="fl on" href="http://202.200.48.93/" >首页<i></i></a>
	        		        		    			        	<a class="fl " href="http://202.200.48.93/?mod=goods&ac=nine" >家具<i></i></a>
	        		        		    			        	<a class="fl " href="http://202.200.48.93/?mod=goods&ac=nineteen" >建材<i></i></a>
	        		        		    			        	<a class="fl " href="http://202.200.48.93/?mod=goods&ac=xiaoguotu" >装修效果图<i></i></a>
	        		        		    			        	<a class="fl " href="http://202.200.48.93/?mod=gift" >积分商城<i></i></a>
	        		        	        	        </div>
    </div>
</div><link href="./extend/gift/static/css/address.css" type="text/css" rel="stylesheet"/>
<div class="area">
  <div class="address clear">
    <h2>收货人信息</h2>
    <div class="toptxt">
    	<ul class="masteraddr-addr">
    	    	</ul>
    </div>
    <div class="masteraddr-content" >
	    <div><em>*</em><label>收货人：</label><input name="address[truename]" type="text" class="name_input"></div>	    
	    <div class="masteraddr-aarea"><em>*</em><label>地&nbsp;&nbsp;区：</label>
	    		<select id="s_province" name="address[province]"></select>
	    		<select id="s_city" name="address[city]"></select>
	    		<select id="s_county" name="address[county]"></select></div>
	    <div class="masteraddr-address"><em>*</em><label>收货地址：</label><input name="address[addr]" type="text" class="address_input"></div>
	    <div><em>*</em><label>手&nbsp;&nbsp;机：</label><input name="address[mobile]" type="text" class="phone_input"></div>
	    <div><em>*</em><label>邮&nbsp;&nbsp;编：</label><input name="address[postcode]" type="text" class="postcode_input"></div>
    	<div class="masteraddr-btn">
    		<a class="masteraddr-cf" href="javascript:void(0)" onclick="save_address();">确认</a>
    		<span></span>
    	</div>
    </div>
  </div>
  
  <!--//配送说明-->

  
  <div class="confcls">
	    <h2>确认订单信息</h2>
	    <form accept-charset="UTF-8" action="http://202.200.48.93/?mod=gift&ac=apply" method="post" onsubmit="return exc_apply('积分');">
	      <input type="hidden" name="userintegral" value="30">
	      <input type="hidden" name="needintegral" value="4627">
	      <div class="gift">
	        <p><span>订单详情</span></p>
	        <div class="clear">
	          <dl class="ginfo">
	            <dt><a href="http://202.200.48.93/?mod=gift&ac=detail&id=1" target="_blank"><img src="http://202.200.48.93/./upload/2015/12/03/17/20151203173515385.jpg"></a></dt>
	            <dd>
	              <span class="maxh40"><a target="_blank" href="http://202.200.48.93/?mod=gift&ac=detail&id=1">210512031</a></span>
	              <span>价值:348.00元</span>
	              <input id="id" name="id" type="hidden" value="1">
	            </dd>
	          </dl>
	        </div>
	      </div>
	      <div class="jadinfo">

		        <input type="hidden" name="formhash" value="052a8ce6">
		        <input class="masteraddr-cf" name="" type="submit" value="确定购买">
		        <p class="clear"/>
	      </div>
	</form>  
	</div>
</div>
<script type="text/javascript" src="static/js/area.js"></script>
<script type="text/javascript">_init_area();</script>
<link href="static/plugins/totop/static/css/common.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript" src="static/plugins/totop/static/js/common.js"></script>
<div style="display: none;" class="rightnfixd rf_het" id="rocket-to-top">
    <span>
    <a href="javascript:void(0);" class="rfixedico rightnfixda1"></a>
    <a class="rfixedico rightnfixda2"></a>
    <a href="javascript:;" onclick="addfavorite({'href':'http://202.200.48.93','title':'天城家居'});" class="rfixedico rightnfixda3"></a>
    </span>
    <i class="rfixedico rightnfixdspan1">
    	<img src="">
    	<b>立即扫一扫</b>
    </i>
</div>
<!--
<link href="./home/template/plugins/totop/static/css/common.css" type="text/css" rel="stylesheet"/>
<div style="display: none;" id="rocket-to-top">
	<div style="opacity:0;display: block;" class="level-2"></div>
	<div class="level-3"></div>
</div>
<script type="text/javascript" src="./home/template/plugins/totop/static/js/common.js"></script>
--><script type="text/javascript" src="static/js/jquery.lazyload.js"></script>
<script type="text/javascript">
//图片异步加载
$("img.lazy").lazyload({threshold:200,failure_limit:30});
</script>
<div class="clear"></div>
<div class="about">
    <ul class="area" style="width: 1020px;">
    	        	            <li class="lw fl">
                <span>关于我们</span>
                                <a href="http://202.200.48.93/?mod=help&ac=info&cid=3&id=1" target="_blank">服务条款</a>
                                <a href="http://202.200.48.93/?mod=help&ac=info&cid=3&id=4" target="_blank">关于我们</a>
                                <a href="http://202.200.48.93/?mod=help&ac=info&cid=3&id=5" target="_blank">诚聘英才</a>
                            </li>
                        <li class="lw fl">
                <span>帮助中心</span>
                                <a href="http://202.200.48.93/?mod=help&ac=info&cid=4&id=2" target="_blank">积分规则</a>
                                <a href="http://202.200.48.93/?mod=help&ac=info&cid=4&id=7" target="_blank">新手上路</a>
                                <a href="http://202.200.48.93/?mod=help&ac=info&cid=4&id=8" target="_blank">常见问题</a>
                            </li>
                        <li class="lw fl">
                <span>商务合作</span>
                                <a href="http://202.200.48.93/?mod=help&ac=info&cid=5&id=9" target="_blank">商家报名</a>
                            </li>
                            <li class="lw w2 fl">
            <span>关注我们</span>
          <!--
                        <a href="#" target="_blank">新浪微博</a>
                                    <a href="#" target="_blank">腾讯微博</a>
                                    <a href="#" target="_blank">QQ空间</a>
                        -->
                    	<a href="#" class="contractKf" title="在线客服" target="_blank">在线客服</a>
                    </li>
        <li class="w3 fl">
            <span>下次怎么来?</span>
            <h3>记住域名：<a href="javascript:void(0);"><em>202.200.48.93</em></a></h3>
            <h5>收藏本站：<a class="clt" href="javascript:void(0);" onclick="return addfavorite(this,'天城家居','http://202.200.48.93');"><u>加入收藏</u></a></h5>
        </li>
      <!--
        <li class="w4 fl">
            <span>关注天城家居</span>
            <h4>
                <img src="" class="fl" alt="" width="72" height="73">
                <p>
                    关注天城家居，秒杀早知道
                    <br>
                    如何关注？
                    <br>
                    1) 查找微信号“<em>jiukuaiwu99</em>”
                    <br>
                    2) 用微信扫描左侧二维码
                </p>
            </h4>
        </li>
        -->
    </ul>
</div>
<div class="clear"></div>
<div class="area footer">
	 Copyright @ 2015 天城家居	<br>
	</div></body>
</html>