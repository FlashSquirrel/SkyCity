<?php
if(!defined('PATH_ROOT')){
    exit('Access Denied');
}
define('SERVER', 'http://s.wangyue.cc/');
class system{
    public static $_webset;
    public static function check_purview(){
        global $access;
        $access = lib_access :: factory(APPNAME);
        $access -> test_purview(MODNAME, ACTNAME);
        $GLOBALS['user'] = $access -> get_instance() -> fields;
    }
    public static function _init(){
        self :: getwebsite();
        $GLOBALS['_webset'] = self :: $_webset;
        if(APPNAME != 'admin' && $GLOBALS['_webset']['site_status'] != 1 && ACTNAME != 'validate_image'){
            exit($GLOBALS['_webset']['site_closed_reason']);
        }
        $GLOBALS['_webset']['site_url'] = trim($GLOBALS['_webset']['site_url'], '/');
        $nav = self :: getnav();
        $GLOBALS['_nav'] = $nav['tag'];
        $GLOBALS['_id_nav'] = $nav['id'];
        unset($nav);
        $GLOBALS['_navtag'] = isset($GLOBALS['_nav'][MODNAME . '/' . ACTNAME]['tag'])?$GLOBALS['_nav'][MODNAME . '/' . ACTNAME]['tag']:'';
        $GLOBALS['_seo'] = self :: getseo();
        $GLOBALS['_link'] = self :: getlink();
        $GLOBALS['_ad'] = self :: getad();
    }
    public static function webset($set, $rec = false){
        if($rec){
            foreach ($set as $key => $value){
                lib_database :: wquery('replace into ' . tname('webset') . ' set `key`=\'' . $key . '\',`val`=\'' . $value . '\'');
            }
        }else{
            foreach ($set as $key => $value){
                lib_database :: update('webset', array('val' => $value), '`key`=\'' . $key . '\'');
            }
        }
        del_cache('config', 'webset');
    }
    public static function getwebsite(){
        if(empty(self :: $_webset) && !isset($GLOBALS['_webset'])){
            $webset = get_cache('config', 'webset');
            if(empty($webset)){
                $query = lib_database :: rquery('select * from ' . tname('webset'));
                while ($value = lib_database :: fetch_one($query)){
                    $webset[$value['key']] = $value['val'];
                }
                set_cache('config', 'webset', $webset, 0);
            }
            self :: $_webset = $webset;
        }
    }
    function check_sun_domain(){
        self :: getwebsite();
        $now_domain = strtolower($_SERVER['SERVER_NAME']);
        $key = array_search($now_domain, self :: $_webset);
        if(!empty($key)){
            $mod = explode('_', $key);
            define('MODNAME', $mod[0]);
        }
    }
    public static function navset($nav){
        if(empty($nav['id'])){
            lib_database :: insert('nav', array_keys($nav), $nav);
        }else{
            $nav['hide'] = intval($nav['hide']);
            $nav['target'] = intval($nav['target']);
            lib_database :: update('nav', $nav, '`id`=\'' . $nav['id'] . '\'');
        }
        del_cache('config', 'nav');
    }
    public static function getnav(){
        $nav = get_cache('config', 'nav');
        if(empty($nav)){
            $query = lib_database :: rquery('select * from ' . tname('nav') . ' order by `sort` desc');
            while ($value = lib_database :: fetch_one($query)){
                $nav['tag'][$value['mod'] . '/' . $value['ac']] = $value;
                $nav['id'][$value['id']] = $value;
            }
            set_cache('config', 'nav', $nav, 0);
        }
        return $nav;
    }
    public static function seoset($seo){
        if(empty($seo['id'])){
            lib_database :: insert('seo', array_keys($seo), $seo);
        }else{
            lib_database :: update('seo', $seo, '`id`=\'' . $seo['id'] . '\'');
        }
        del_cache('config', 'seo');
    }
    public static function getseo(){
        $seo = get_cache('config', 'seo');
        if(empty($seo)){
            $query = lib_database :: rquery('select * from ' . tname('seo'));
            while ($value = lib_database :: fetch_one($query)){
                $seo[$value['mod'] . '_' . $value['ac']] = $value;
            }
            set_cache('config', 'seo', $seo, 0);
        }
        return $seo;
    }
    public static function linkset($link){
        if(empty($link['id'])){
            lib_database :: insert('link', array_keys($link), $link);
        }else{
            lib_database :: update('link', $link, '`id`=\'' . $link['id'] . '\'');
        }
        del_cache('advertise', 'link');
    }
    public static function getlink(){
        $link = get_cache('advertise', 'link');
        if(empty($link)){
            $query = lib_database :: rquery('select * from ' . tname('link') . ' order by `sort` DESC');
            while ($value = lib_database :: fetch_one($query)){
                $link[$value['id']] = $value;
            }
            set_cache('advertise', 'link', $link, 0);
        }
        if(V_MODE != 'vip'){
            $link[] = array('isindex' => 1, 'title' => '惠贝网', 'url' => 'http://www.huibe.com');
            $link[] = array('isindex' => 1, 'title' => '新品网', 'url' => 'http://www.uoniu.com');
        }
        return $link;
    }
    public static function adset($ad){
        global $_timestamp;
        if(empty($ad['id'])){
            $ad['addtime'] = $_timestamp;
            lib_database :: insert('advertise', array_keys($ad), $ad);
        }else{
            lib_database :: update('advertise', $ad, '`id`=\'' . $ad['id'] . '\'');
        }
        del_cache('advertise', 'ad');
    }
    public static function getad(){
        $ad = get_cache('advertise', 'ad');
        if(empty($ad)){
            $query = lib_database :: rquery('select * from ' . tname('advertise'));
            while ($value = lib_database :: fetch_one($query)){
                $ad['ad_' . $value['type']][$value['id']] = $value;
            }
            set_cache('advertise', 'ad', $ad, 0);
        }
        return $ad;
    }
    public function connectinstall($key, $config){
        $connect = self :: getconnect();
        if(!file_exists(PATH_API . '/fastlogin/' . $key . '/config.php')){
            exit("文件不存在");
        }
        require PATH_API . '/fastlogin/' . $key . '/config.php';
        $connect[$key] = array('config' => $config, 'install' => 1, 'name' => $modules[$key]['name']);
        system :: webset(array('connect' => serialize($connect)));
    }
    public function connectuninstall($key){
        $connect = self :: getconnect();
        unset($connect[$key]);
        system :: webset(array('connect' => serialize($connect)));
    }
    public function getconnect($key = ""){
        global $_webset;
        $connect = unserialize($_webset['connect']);
        if(!empty($key)){
            if(!isset($connect) || empty($connect)){
                $connect = array();
            }else{
                return $connect[$key];
            }
        }else{
            if(!isset($connect) || empty($connect)){
                $connect = array();
            }
            return $connect;
        }
    }
    public function getcatgather($cid = 0){
        $gathercid = get_cache('gather', 'gathercid');
        if(empty($gathercid)){
            $query = lib_database :: rquery('select * from ' . tname('type_gather'));
            while ($value = lib_database :: fetch_one($query)){
                $gathercid[$value['cid']] = $value;
            }
            set_cache('gather', 'gathercid', $gathercid, 0);
        }
        if(!empty($cid)){
            return $gathercid[$cid];
        }else{
            return $gathercid;
        }
    }
    public function boutiqueparameter($task, $page_no = 1){
        global $_webset;
        $getcatboutique = system :: getcatgather();
        foreach ($task as $key => $value){
            $parameter[$key]['nav'] = $value['rule']['nav'];
            foreach ($value['rule']['cat'] as $k => $val){
                if(isset($getcatboutique[$val])){
                    $tocat = unserialize($getcatboutique[$val]['boutiquecat']);
                    if(!empty($tocat)){
                        $parameter[$key]['cat'][] = implode(',', $tocat);
                    }
                }
            }
            $parameter[$key]['cat'] = implode(',', $parameter[$key]['cat']);
            $parameter[$key]['page_size'] = '20';
            $parameter[$key]['page_no'] = $page_no;
            $parameter[$key]['channel'] = $value['nav'];
        }
        return json_encode($parameter);
    }
    public function admincenter(){
        global $_webset;
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $charset = strtoupper($config['charset']);
        $update = date('Ymd', $_webset['site_update']);
        $version = V_MODE . '_' . $_webset['site_version'];
        //$bulletin = SERVER . 'admin/?mod=yun&ac=show&op=bulletin';
        //$authorize = SERVER . 'admin/?mod=yun&ac=show&op=authorize&d=' . $domain . '&v=' . $version;
        //$version = SERVER . 'admin/?mod=yun&ac=show&op=version';
        echo '<div class="con" style="width: 30%;float:right">
    	<div class="table">
            <h2 class="th">版本/授权信息 <span class="head"></h2>                
            <table cellspacing="0" width="100%" style="border:1px  solid #DCEAF7;">
	            <tbody>
	            <tr>
	              <td colspan="2" valign="top">
	              	<span class="left_txt" style="color:#F00; font-weight:bold">
	              	&nbsp; 当前版本：<span class="banben">' . V_MODE . '-V' . $_webset['site_version'] . '</span>
	              	' . $charset . ' 更新日期：
	              	</span> 
	              	<span class="S3" style="color:#F00; font-weight:bold">' . $update . '</span>
				    <div style=" margin:5px 10px; clear:both; height:50px; line-height:20px; vertical-align:middle">
				      <iframe frameborder="0" marginheight="0" marginwidth="0" border="0" scrolling="No" height="50" width="100%" src="http://www.uoniu.com/app/sq/banben.html"></iframe>
		          </div></td>
	            </tr>
	          </tbody></table>
        </div>
        <div class="mb10"></div>
        <div class="table">
        <h2 class="th">&nbsp;商业授权查询&nbsp;&nbsp;<span style="font-size:12px; font-weight:normal"></span> </h2>                
        <table cellspacing="0" width="100%" style="border:1px  solid #DCEAF7;">
	            <tbody>
	            <tr>
	              <td colspan="2" valign="top" style="padding:3px 10px;">
	              <iframe frameborder="0" marginheight="0" marginwidth="0" border="0" scrolling="No" height="40" width="100%" src="http://www.uoniu.com/app/sq/"></iframe>	  
	              </td>
	            </tr>
	          </tbody></table>
        </div>
        <div class="mb10"></div>
        <div class="table">
        <h2 class="th">&nbsp;官方公告</h2>          
        <table cellspacing="0" width="100%" style="border:1px  solid #DCEAF7;">
            <tbody><tr>
              <td colspan="2" valign="top" style="padding:3px 10px;">
	 		   <iframe frameborder="0" marginheight="0" marginwidth="0" border="0" scrolling="No" height="160" width="100%" src="http://www.uoniu.com/app/sq/gonggao.html"></iframe>	  
			  </td>
            </tr>
          </tbody></table>
        </div>
    <p style="clear:both"></p>
</div>';
    }
    public function admin_login(){
        global $_webset, $config;
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $charset = strtoupper($config['charset']);
        //$version = V_MODE . '_' . $_webset['site_version'];
        //$version = SERVER . 'admin/?mod=yun&ac=show&op=login&d=' . $domain . '&v=' . $version;
        //return '<script>$(function(){ajaxOperating("http://s.wangyue.cc/admin/?mod=yun&ac=show&op=login",{},"GET","jsonp","login_set_callback");});function login_set_callback(json){$("#login_set").html(json.html);}</script>';
    }
    public function source_jsnav($callback = 'set_source_nav'){
        global $_webset;
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $_webset['site_version'] = '1.2.2';
        $url = SERVER . 'gather.php?d=' . $domain . '&v=' . $_webset['site_version'] . '&api=nav';
        return "jQuery.getScript(\"" . $url . "\",
	function(){
		//执行采集
		source_nav('{$callback}');
	});";
    }
    public function source_jscat($callback = 'set_source_cat'){
        global $_webset;
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $_webset['site_version'] = '1.2.2';
        $url = SERVER . 'gather.php?d=' . $domain . '&v=' . $_webset['site_version'] . '&api=cat';
        return "jQuery.getScript(\"" . $url . "\",
	function(){
		//执行采集
		source_cat('{$callback}');
	});";
    }
    public function boutique_jsgather(){
        global $_webset;
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $url = SERVER . 'gather.php?d=' . $domain . '&v=' . $_webset['site_version'] . '&api=boutique';
        return "jQuery.getScript(\"" . $url . "\");";
    }
    public function brands_jsgather(){
        global $_webset;
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $url = SERVER . 'gather.php?d=' . $domain . '&v=' . $_webset['site_version'] . '&api=brands';
        return "jQuery.getScript(\"" . $url . "\",function(){});";
    }
    public function getgoods_js(){
        global $_webset, $_timestamp;
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $url = SERVER . 'gather.php?d=' . $domain . '&v=' . $_webset['site_version'] . '&api=getgoods';
        return "function system_getgoods(numid){
		var callback =arguments[1]?arguments[1]:'setgoods';
		jQuery.getScript(\"" . $url . "\",
		function(){
			get_goods(numid,callback);
		});}";
    }
    public function get_goodsimg_js(){
        global $_webset, $_timestamp;
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $url = SERVER . 'gather.php?d=' . $domain . '&v=' . $_webset['site_version'] . '&api=getgoodsimg';
        return "var check_img_eq=0;
		function system_getgoodsimg(numid){
		var callback_a =arguments[1]?arguments[1]:'set_img_list';
		jQuery.getScript(\"" . $url . "\",
		function(){
			get_img_list(numid,callback_a);
		});}";
    }
    public function check($mod_name = '', $type = ''){
        /*global $_webset, $_timestamp, $_hash;
        if(empty($_hash) || $_hash != hash_file('md5', CADBEAACFFBDAE)){
            system :: show_check();
        }
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $version = V_MODE . '_' . $_webset['site_version'];
        $check_code = get_cache('authorize', 'certificate' . $type . $mod_name);
        if(defined('V_MODE') && V_MODE == 'vip' && (empty($check_code) || $check_code != md5(md5($domain) . md5($version) . date('Yz', $_timestamp)))){
            $authorize = SERVER . 'admin/?mod=yun&ac=authorize&d=' . $domain . '&v=' . $version . '&t=' . $_timestamp . '&pmod=' . $mod_name . '&type=' . $type;
            $authorizedata = curl($authorize);
            $authorizedata = json_decode($authorizedata, true);
            if($authorizedata['code'] == -1){
                system :: show_check($authorizedata['error']);
            }else{
                set_cache('authorize', 'certificate' . $type . $mod_name, $authorizedata['certificate'], $authorizedata['certificatetime']);
            }
        }*/
    }
    public function show_check($msg = ''){
        //$msg = empty($msg)?"你未被授权":$msg;
        //echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>' . $msg . '</title></head><body style="background: #117881;"><img src="http://s.wangyue.cc/static/images/noautho.jpg" style="margin: 0px auto;margin-top:80px;display: block;"></body></html>';
        //exit();
    }
}
function statistics(){
    //global $_isajax;
    //if(V_MODE != 'vip' && APPNAME != 'admin' && !$_isajax && MODNAME != 'ajax' && MODNAME != 'validate_image' && MODNAME != 'desktop'){
        //echo '<!----><div style="display:none"><script language="javascript" type="text/javascript" src="http://js.users.51.la/17851949.js"></script></div>';
    //}
}
?>