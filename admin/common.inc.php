<?php
if(!defined('PATH_ROOT')){exit('Access Denied');} define('PATH_TPL','./template'); define('PATH_FRONT_TPL','../home/template'); require './lib/func/common.func.php'; system::_init(); system::check_purview(); define('DEF_GD_LOGO',empty($_webset['site_goodlogo'])?'../static/images/default.gif':$_webset['site_goodlogo']); if(ACTNAME=='welcome'){ include(PATH_TPL."/public/header.tpl.php"); ?>
<style type="text/css">._help a{width: 80px;display: block;float: left;text-align: center;}</style>
<div class="box-content">

   <!-- <?=system::admincenter();?>
 -->   
    <div class="con" style="width: 68%;">
    	<div class="table">

            <table>
                <tr>
                    <td width="60">常用操作:</td>
                    <td class="_help">
                    	<a href="?mod=main&ac=goods&op=add" target="main">增加产品</a>
                    	<a href="?mod=user&ac=index" target="main">会员列表</a>
                    	<a href="?mod=main&ac=exc&op=list" target="main">兑换管理</a>
                    	<a href="?mod=article&ac=list" target="main">文章管理</a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="mb10"></div>
    	<div class="table">
            <h2 class="th">待处理 </h2>                
            <table>
                <tr>
                    <td width="400">宝贝报名:<strong class="red goods_apply">0</strong></td>

                    <td width="400">兑换报名:<strong class="red try_exchange">0</strong></td>
                </tr>
                <tr>
                    <td>评论审核:<strong class="red comment_audit">0</strong></td>
                  <td>礼品兑换:<strong class="red exchange_audit">0</strong></td>
                </tr>
            </table>
        </div>
        <div class="mb10"></div>
        <div class="table">
            <h2 class="th">用户数据</h2>                
            <table>
            	<tr>
                    <td width="400">会员数量:<strong class="red user">0</strong></td>
                    <td width="400">今日注册:<strong class="red today_user">0</strong></td>
                    <td width="400">今日签到:<strong class="red today_sign">0</strong></td>
                </tr>
                <tr>
                    <td>用户评论:<strong class="red comment">0</strong></td>
                    <td>兑换晒单:<strong class="red exchange_sun">0</strong></td>
                </tr>
            </table>
        </div>
        <div class="mb10"></div>
        <div class="table">
            <h2 class="th">数据信息</h2>                
            <table>
            	<tr>
                    <td width="400">宝贝总数:<strong class="red goods_num">0</strong></td>
                    <td width="400">今日上新:<strong class="red today_goods_num">0</strong></td>
                    <td width="400">抢光了:<strong class="red issteal_goods_num">0</strong></td>
                </tr>
            	<tr>
                    <td>过期宝贝:<strong class="red over_goods_num">0</strong></td>
                    <td>未开始宝贝:<strong class="red nostart_goods_num">0</strong></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
//处理数据
ajaxOperating('?mod=ajax&ac=operat&op=center&d=<?=date('z');?>','','GET','jsonp','setcenter',false);
function setcenter(json){
	$(".goods_apply").text(json.goods_apply);
	$(".try_apply").text(json.try_apply);
	$(".try_exchange").text(json.try_exchange);
	$(".comment_audit").text(json.comment_audit);
	$(".try_audit").text(json.try_audit);
	$(".exchange_audit").text(json.exchange_audit);

	$(".user").text(json.user);
	$(".today_user").text(json.today_user);
	$(".today_sign").text(json.today_sign);
	$(".comment").text(json.comment);
	$(".try_sun").text(json.try_sun);

	$(".goods_num").text(json.goods_num);
	$(".today_goods_num").text(json.today_goods_num);
	$(".issteal_goods_num").text(json.issteal_goods_num);
	$(".over_goods_num").text(json.over_goods_num);
	$(".nostart_goods_num").text(json.nostart_goods_num);
}
</script>
<?php   include(PATH_TPL."/public/footer.tpl.php"); exit(); }  ?>