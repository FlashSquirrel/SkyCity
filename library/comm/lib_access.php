<?php
if(!defined('PATH_ROOT')){
    exit('Access Denied');
}
@session_start();
@header('Cache-Control:private');
require PATH_LIBRARY . '/comm/lib_access_cfg.php';
require PATH_LIBRARY . '/comm/lib_validate.php';
class lib_access{
    public $app_name = '';
    public static $cfg_groups = '';
    public $acc_hand = '';
    public $uid = 0;
    public $fields = array();
    public $user_apps = '';
    public $user_groups = '';
    public $user_purview_mods = '';
    public $return_url = '';
    public static $accctl = null;
    public static $user_table = 'users';
    public static $cfg_cache = 'acc_cfg';
    public static $info_cache = 'accuser';
    function __construct($app_name){
        self :: $user_table = tname(self :: $user_table);
        self :: $cfg_groups = lib_access_cfg :: get_config();
        $this -> acc_hand = $GLOBALS['config']['cookie_pre'];
        if(!isset(self :: $cfg_groups['apps'][$app_name])){
            exit("Setting `{$app_name}` is not Found! ");
        }
        $this -> app_name = $app_name;
        if(self :: $cfg_groups['apps'][$app_name]['auttype'] == 'session'){
            $this -> uid = isset($_SESSION[$this -> acc_hand . APPNAME . '_uid']) ? $_SESSION[$this -> acc_hand . APPNAME . '_uid'] : 0;
        }else if(self :: $cfg_groups['apps'][$app_name]['auttype'] == 'cookie'){
            $this -> uid = ($this -> get_cookie(APPNAME . '_uid') == '') ? 0 : intval($this -> get_cookie(APPNAME . '_uid'));
        }else{
            $this -> uid = isset($_SESSION[$this -> acc_hand . APPNAME . '_uid']) ? $_SESSION[$this -> acc_hand . APPNAME . '_uid'] : 0;
            $this -> uid = empty($this -> uid) ? ($this -> get_cookie(APPNAME . '_uid') == '') ? 0 : intval($this -> get_cookie(APPNAME . '_uid')) : $this -> uid;
        }
        $this -> get_userinfos();
        self :: $accctl = $this;
    }
    public static function factory($appname, $cp_url = ''){
        if(self :: $accctl === null){
            self :: $accctl = new lib_access($appname);
        }
        return self :: $accctl;
    }
    public static function get_instance(){
        return self :: $accctl;
    }
    public function set_control_url($url){
        self :: $cfg_groups['apps'][$this -> app_name]['control'] = $url;
    }
    public function get_control_url(){
        global $_webset;
        $referer = get_cururl();
        if(!empty($referer) && $_webset['rewrite_open'] < 1 && !strpos('http://', $referer)){
            $mod = $ac = '';
            $referer = str_replace('/?', '', $referer);
            $referer = explode('&', $referer);
            $referer_arr = array();
            foreach ($referer as $val){
                $val = explode('=', $val);
                if($val[0] == 'mod'){
                    $mod = $val[1];
                }elseif($val[0] == 'ac'){
                    $ac = $val[1];
                }else{
                    $referer_arr[$val[0]] = $val[1];
                }
            }
            $referer = u($mod, $ac, $referer_arr);
        }
        preg_match('/\?mod=(.*)&ac=(.*)/', self :: $cfg_groups['apps'][$this -> app_name]['control'], $patterns);
        if(defined('APPNAME') && APPNAME == 'admin'){
            return self :: $cfg_groups['apps'][$this -> app_name]['control'] . '&gourl=' . base64_encode($referer);
        }else{
            return u($patterns[1], $patterns[2], array('gourl' => base64_encode($referer)));
        }
    }
    public function get_userinfos($cache = false){
        $user_table = self :: $user_table;
        if(!empty($this -> uid)){
            if(APPNAME == 'admin'){
                $query = "Select ut.* From `{$user_table}` ut where ut.uid='{$this->uid}' and ut.apps='" . APPNAME . "'";
            }else{
                $query = "Select ut.* From `{$user_table}_" . APPNAME . "_session` ut where ut.uid='{$this->uid}' and ut.apps='" . APPNAME . "'";
            }
            $formcache = false;
            if($cache){
                $this -> fields = get_cache(self :: $info_cache, $this -> uid);
                if(empty($this -> fields)){
                    $this -> fields = lib_database :: get_one($query);
                }else{
                    $formcache = true;
                }
            }else{
                $this -> fields = lib_database :: get_one($query);
                if(APPNAME != 'admin'){
                    $query = 'UPDATE `' . $user_table . '_' . APPNAME . '_session` set `lastactivity`=\'' . $GLOBALS['_timestamp'] . '\' where uid=\'' . $this -> uid . '\'';
                    lib_database :: wquery($query);
                }
            }
            if(is_array($this -> fields)){
                $this -> uid = $this -> fields['uid'];
                $this -> user_apps = $this -> fields['apps'];
                $this -> user_groups = $this -> fields['groups'];
                if(!$formcache){
                    set_cache(self :: $info_cache, $this -> uid, $this -> fields, 1800);
                }
                return $this -> fields;
            }else{
                $this -> uid = 0;
            }
        }else{
            $this -> uid = 0;
        }
        return false;
    }
    public function del_cache($uid){
        del_cache(self :: $info_cache, $uid);
    }
    public function test_purview($mod, $action, $backtype = false){
        $rs = 0;
        $public_mod = isset(self :: $cfg_groups['apps'][$this -> app_name]['public'][$mod]) ? self :: $cfg_groups['apps'][$this -> app_name]['public'][$mod] : array();
        if(!empty(self :: $cfg_groups['apps'][$this -> app_name]['public']) && (self :: $cfg_groups['apps'][$this -> app_name]['public'] == '*' || in_array($action, $public_mod) || in_array('*', $public_mod))){
            $rs = 1;
        }else if(empty($this -> uid)){
            $rs = 0;
        }else{
            $protected_mod = isset(self :: $cfg_groups['apps'][$this -> app_name]['protected'][$mod]) ? self :: $cfg_groups['apps'][$this -> app_name]['protected'][$mod] : array();
            if (!empty(self :: $cfg_groups['apps'][$this -> app_name]['protected']) && (self :: $cfg_groups['apps'][$this -> app_name]['protected'] == '*' || in_array($action, $protected_mod) || in_array('*', $protected_mod))){
                $rs = 1;
            }else{
                $apps = explode(',', self :: $cfg_groups['apps'][$this -> user_apps]['allowapp']);
                $apps[] = $this -> user_apps;
                if(!in_array($this -> app_name, $apps)){
                    $rs = -2;
                }else{
                    if(empty($this -> user_purview_mods)){
                        $this -> user_purview_mods = $this -> _check_purview_mods();
                    }
                    if($this -> user_purview_mods != '#'){
                        $user_purviews = isset($this -> user_purview_mods[$this -> app_name]) ? $this -> user_purview_mods[$this -> app_name] : '';
                    }else{
                        $user_purviews = '#';
                    }
                    if($user_purviews == '#'){
                        $rs = -1;
                    }else if($user_purviews == '*'){
                        $rs = 1;
                    }else{
                        if(is_array($user_purviews) && isset($user_purviews[$mod]) && (in_array($action, $user_purviews[$mod]) || in_array('*', $user_purviews[$mod]))){
                            $rs = 1;
                        }else{
                            $rs = -1;
                        }
                    }
                }
            }
        }
        if($backtype){
            return $rs;
        }else{
            if($rs == 1){
                return true;
            }else if($rs == -1){
                @header('Content-Type: text/html; charset=utf-8');
                exit('权限不足, 对不起，你没权限执行本操作！');
            }else if($rs == -2){
                $jumpurl = $this -> get_control_url();
                header("Location:$jumpurl");
                exit();
            }else{
                $jumpurl = $this -> get_control_url();
                header("Location:$jumpurl");
                exit();
            }
        }
    }
    protected function _check_purview_mods(){
        $rs = '';
        $userGroups = explode(',', $this -> user_groups);
        if(!is_array($userGroups)){
            $rs = '#';
        }
        foreach($userGroups as $userGroup){
            list($appname, $gp) = explode('-', $userGroup);
            if(isset(self :: $cfg_groups['apps'][$appname]['private'][$gp]['allow'])){
                $rs[$appname] = self :: $cfg_groups['apps'][$appname]['private'][$gp]['allow'];
            }
        }
        if(!is_array($rs)){
            $rs = '#';
        }
        return $rs;
    }
    public function loginout(){
        if(APPNAME != 'admin'){
            $fields = self :: get_instance() -> fields;
            if(!empty($fields['uid'])){
                $user_table = self :: $user_table;
                lib_database :: wquery('DELETE FROM `' . $user_table . '_' . APPNAME . '_session` WHERE uid=' . $fields['uid']);
            }
        }
        $_SESSION['uid'] = '';
        session_destroy();
        $this -> _drop_cookie(session_id());
        $this -> _drop_cookie('uid');
        return true;
    }
    public function check_user($account, $loginpwd, $keeptime = 86400){
        global $_webset;
        $user_table = self :: $user_table;
        $ftype = 'user_name';
        if(lib_validate :: email($account)){
            $ftype = 'email';
        }else if(!lib_validate :: user_name($account)){
            return array('cod' => 0, 'msg' => '会员名格式不合法');
        }
        $adds = " and apps='" . ($this -> app_name) . "' ";
        $row = lib_database :: get_one("Select * From `{$user_table}` where `{$ftype}` like '{$account}' $adds ");
        if(is_array($row)){
            $row['accounts'] = $account;
            if($row['sta'] == 0 && $_webset['site_activation'] == 1 && $this -> app_name == 'home'){
                return array('code' => -2, 'msg' => '邮箱未激活！<a target="_blank" href="' . u('user', 'activation', array('op' => 'againactivation', 'email' => $row['email'])) . '">立即激活</a>');
            }else if($row['userpwd'] != $this -> _get_encodepwd($loginpwd)){
                return array('code' => -1, 'msg' => '密码错误！');
            }else{
                $this -> _put_logininfo($row, $keeptime);
                return array('code' => 1, 'msg' => '');
            }
        }else{
            return array('code' => 0, 'msg' => '用户不存在！');
        }
    }
    public function keep_user($account, $actype = 'uid', $keeptime = 86400){
        $user_table = self :: $user_table;
        $expr = $actype == 'uid' ? '=' : 'like';
        $row = lib_database :: get_one("Select * From `{$user_table}` where `{$actype}` {$expr} '{$account}' ");
        if(!is_array($row)){
            return false;
        }
        $this -> _put_logininfo($row, $keeptime);
        $this -> uid = $row['uid'];
        $this -> get_userinfos();
        return true;
    }
    public function get_login_error24($accounts){
        global $_ip;
        $user_table = self :: $user_table;
        $error_num = 5;
        $day_starttime = strtotime("today");
        $hash = md5($accounts . '-' . $_ip);
        $query = "Select SQL_CALC_FOUND_ROWS `loginsta` From `{$user_table}_login_history` where `hash`='{$hash}'
                  And `logintime` > {$day_starttime} order by `logintime` desc limit {$error_num}";
        $rc = lib_database :: query($query);
        $info_row = lib_database :: get_one(' SELECT FOUND_ROWS() as dd ');
        if($info_row['dd'] < $error_num){
            return false;
        } while($row = lib_database :: fetch_one($rc)){
            if($row['loginsta'] > 0){
                return false;
            }
        }
        return true;
    }
    public function save_login_history(& $row, $loginsta){
        global $_ip;
        $ltime = time();
        $user_table = self :: $user_table;
        if(!isset($row['accounts'])){
            $row['accounts'] = $row['user_name'];
        }
        $hash = md5($row['accounts'] . '-' . $_ip);
        $row['uid'] = isset($row['uid']) ? $row['uid'] : 0;
        lib_database :: query("Update `{$user_table}` set `logintime`='{$ltime}', `loginip`='{$_ip}' where `uid` = '{$row['uid']}' ");
        $query = "INSERT INTO `{$user_table}_login_history` (`uid`, `accounts`, `loginip`, `logintime`, `apps`, `loginsta`, `hash`)
                  VALUES('{$row['uid']}', '{$row['accounts']}', '{$_ip}', '{$ltime}', '{$this->app_name}', '{$loginsta}', '{$hash}'); ";
        $q = lib_database :: query($query, true);
    }
    protected function _put_logininfo(& $row, $keeptime = 0){
        global $_ip;
        $user_table = self :: $user_table;
        $ltime = time();
        $this -> uid = $row['uid'];
        if(APPNAME != 'admin'){
            $user_fields = lib_database :: get_one('select b.* from ' . $user_table . '_' . APPNAME . '_fields as b WHERE b.uid=' . $this -> uid);
            if(empty($user_fields)){
                $user_fields = array();
                lib_database :: wquery('insert ignore `' . $user_table . '_' . APPNAME . '_fields` (`uid`) VALUES (' . $this -> uid . ')');
            }
            unset($row['userpwd'], $row['accounts']);
            $user_fields = array_merge($user_fields, $row);
            $user_fields['lastactivity'] = $GLOBALS['_timestamp'];
            $_fields = '`' . implode('`, `', array_keys($user_fields)) . '`';
            $_data = lib_database :: format_insert_data($user_fields);
            lib_database :: wquery('update `' . self :: $user_table . '` set `logintime`=' . $GLOBALS['_timestamp'] . ',`loginip`=\'' . $_ip . '\' where uid=' . $this -> uid);
            lib_database :: wquery('insert ignore `' . $user_table . '_' . APPNAME . '_session` (' . $_fields . ') VALUES ' . $_data);
            lib_database :: wquery('delete from `' . self :: $user_table . '_' . APPNAME . '_session` where lastactivity<' . ($GLOBALS['_timestamp'] - $GLOBALS['config']['onlinehold']));
        }
        if(self :: $cfg_groups['apps'][$this -> app_name]['auttype'] == 'session'){
            $_SESSION[$this -> acc_hand . APPNAME . '_uid'] = $this -> uid;
            $this -> _put_cookie(session_id(), session_id(), $keeptime, false);
        }
        $this -> _put_cookie(APPNAME . '_uid', $this -> uid, $keeptime);
        return $ltime;
    }
    public function _get_encodepwd($pwd){
        return md5($pwd);
    }
    protected function _put_cookie($key, $value, $keeptime = 0, $encode = true){
        _put_cookie($key, $value, $keeptime = 0, $encode = true);
    }
    protected function _drop_cookie($key, $encode = true){
        _drop_cookie($key, $encode = true);
    }
    public function get_cookie($key, $encode = true){
        return get_cookie($key, $encode = true);
    }
    public static function get_login_infos($uid){
        $user_table = self :: $user_table;
        lib_database :: query("Select `loginip`,`logintime` From `{$user_table}_login_history` where uid='$uid' And loginsta=1 order by `logintime` desc limit 0,2 ");
        $datas = lib_database :: fetch_all();
        if(isset($datas[1])){
            return $datas[1];
        }else{
            return array('loginip' => '', 'logintime' => 0);
        }
    }
    public function get_cli_ip(){
        global $_ip;
        return $_ip;
    }
    public static function save_log($user_name, $msg, $isalert = false, $msg_hash = ''){
        $operate_log = PATH_DATA . '/log/user_admin_operate.log';
        $user_table = self :: $user_table;
        $isalert = $isalert ? 1 : 0;
        if($isalert == 1 && $msg_hash != ''){
            $row = lib_database :: get_one("Select * From `{$user_table}_admin_log` where `msg_hash`='{$msg_hash}' And `isread`=0 ");
            if(is_array($row)){
                return true;
            }
        }
        $cur_time = time();
        $logmsg = "用户：{$user_name} 时间：" . date('Y-m-d H:i:s', $cur_time) . " ||内容：{$msg} \n----------------------------------------\n";
        $fp = fopen($operate_log, 'a');
        fwrite($fp, $logmsg);
        fclose($fp);
        $msg = addslashes($msg);
        $iquery = "Insert into `{$user_table}_admin_log`(`user_name`, `operate_msg`, `operate_time`, `isalert`, `msg_hash`, `isread`)
                                    values('{$user_name}', '{$msg}', " . time() . ", '{$isalert}', '{$msg_hash}', 0);";
        $rs = lib_database :: query($iquery, true);
        return $rs;
    }
    private static function _get_trim_atts(& $atts, $key){
        if(!isset($atts[$key])){
            return '';
        }else{
            return preg_replace("/[ \t\r\n]/", '', $atts[$key]);
        }
    }
    public function falselogin($data){
        global $_timestamp;
        $row = lib_database :: get_one('select * from ' . self :: $user_table . '_token where hash=\'' . $data['hash'] . '\' and apps=\'' . APPNAME . '\'');
        $uid = $row['uid'];
        if(empty($uid)){
            $user_name = '';
            if(!empty($data['synchronous'])){
                if(lib_validate :: user_name($data['user_name'])){
                    if(!check_account_exist($data['user_name'], 'user_name')){
                        $user_name = $data['user_name'];
                    }
                }
            }
            $uid = save_user(array('user_name' => $user_name));
            if(!empty($uid)){
                lib_database :: insert('users_token', array('uid', 'apps', 'name', 'token', 'api', 'apiuid', 'hash'), array($uid, APPNAME, $data['user_name'], $data['token'], $data['api'], $data['apiuid'], $data['hash']));
                $func = 'hook_' . APPNAME . '_falselogin';
                if(function_exists($func)){
                    $func($data);
                }
            }
        }else{
            lib_database :: update('users_token', array('name' => $data['user_name'], 'token' => $data['token']), 'uid=' . $uid . ' and apps=\'' . APPNAME . '\' AND api=\'' . $data['api'] . '\' AND hash=\'' . $data['hash'] . '\'');
        }
        self :: keep_user($uid);
        return true;
    }
}
?>