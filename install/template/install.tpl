<!--//安装锁定-->
<?php if(check_install()){ ?>
<div class="main" style="margin-top: -123px;">
	<b>
	 安装锁定，已经安装过了，如果您确定要重新安装，请到服务器上删除<br>
	 <?=$lockfile;?>
	</b>
	<ul style="line-height: 200%; margin-left: 30px;"></ul><br>
	<span class="red">您必须解决以上问题，安装才可以继续</span><br><br><br>
</div>
<?php }else{ ?>
<!--//安装协议-->
<div class="main" style="margin-top:-123px;">
	<div class="licenseblock">
	<div class="license">
		<h1>授权协议</h1>

	<p>版权所有 (c) 2015，天城家居</p>
	</div></div>
		<div class="btnbox marginbot">
			<form method="get" autocomplete="off" action="index.php">
			<input type="hidden" name="op" value="step1">
			<input type="submit" name="submit" value="我同意" style="padding: 2px">&nbsp;
			<input type="button" name="exit" value="我不同意" style="padding: 2px" onclick="javascript: window.close(); return false;">
			</form>
		</div>
</div>
<?php } ?>