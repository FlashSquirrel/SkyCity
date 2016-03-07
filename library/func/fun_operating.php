<?php
if(!defined('PATH_ROOT')){
    exit('Access Denied');
}
function getTaobaoCatList($id = 0, $level = 0){
    $wherestr = 'parent_cid=\'' . $id . '\'';
    $type_arr = lib_database :: select('type_taobao', '*', $wherestr . ' order by cid asc');
    $level++;
    $typearr = array();
    if(!empty($type_arr)){
        foreach ($type_arr as $cat){
            $cat['level'] = $level;
            if($cat['is_parent'] == 1){
                $typearr[$cat['parent_cid']][$cat['cid']] = $cat;
                $children = getTaobaoCatList($cat['cid'], $level);
                if(!empty($children)){
                    $typearr[$cat['cid']] = $children;
                }
            }else{
                $typearr[$cat['cid']] = $cat;
            }
        }
    }
    return $typearr;
}
function getCatList($type, $id = 0, $level = 0){
    $where[] = 'type=\'' . $type . '\'';
    $where[] = 'pid=\'' . $id . '\'';
    $wherestr = implode(' and ', $where);
    $type_arr = lib_database :: select('type', '*', $wherestr . ' order by `sort` asc');
    $typearr = array();
    $level++;
    if(!empty($type_arr)){
        foreach ($type_arr as $cat){
            $cat['level'] = $level;
            $typearr['cid_' . $cat['id']] = $cat;
            $children = getCatList($type, $cat['id'], $level);
            $typearr = !empty($children)?array_merge($typearr, $children):$typearr;
        }
    }
    return $typearr;
}
function typeAdd($cat){
    global $_timestamp;
    if(!empty($cat['id'])){
        lib_database :: update('type', $cat, 'id=\'' . $cat['id'] . '\'');
    }else{
        $cat['addtime'] = $_timestamp;
        lib_database :: insert('type', array_keys($cat), $cat);
        $cat['id'] = lib_database :: insert_id();
    }
    del_cache('article', 'cat');
    return $cat['id'];
}
function goodslist($wherestr = array(), $order = '', $start = 0, $num = PAGE, $refuse = false){
    global $_timestamp, $_webset;
    $intkeys = array('channel' => 'channel', 'cat' => 'cat', 'ispost' => 'ispost', 'isrec' => 'isrec', 'ispaigai' => 'ispaigai', 'issteal' => 'issteal', 'isvip' => 'isvip', 'status' => 'status', 'aid' => 'aid');
    $strkeys = array();
    $randkeys = array();
    $likekeys = array('title' => 'keyword', 'num_iid' => 'keyword', 'nick' => 'keyword');
    if($refuse){
        $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys, 'a.');
    }else{
        $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
    }
    !empty($search['wherearr']['channel'])?$where[] = $search['wherearr']['channel']:'';
    !empty($search['wherearr']['cat'])?$where[] = $search['wherearr']['cat']:'';
    !empty($search['wherearr']['ispost'])?$where[] = $search['wherearr']['ispost']:'';
    !empty($search['wherearr']['isrec'])?$where[] = $search['wherearr']['isrec']:'';
    !empty($search['wherearr']['ispaigai'])?$where[] = $search['wherearr']['ispaigai']:'';
    !empty($search['wherearr']['issteal'])?$where[] = $search['wherearr']['issteal']:'';
    !empty($search['wherearr']['isvip'])?$where[] = $search['wherearr']['isvip']:'';
    $type = request('type', 0);
    if(!empty($type)){
        if($type == 1){
            $where[] = 'start<' . $_timestamp . ' and end>' . $_timestamp;
        }elseif ($type == -1){
            $where[] = 'end<' . $_timestamp;
        }elseif ($type == 2){
            $where[] = 'start>' . $_timestamp;
        }
    }
    !empty($where)?$wherestr[] = '(' . implode(' AND ', $where) . ')':'';
    unset($where);
    isset($search['wherearr']['title'])?$where[] = $search['wherearr']['title']:'';
    isset($search['wherearr']['num_iid'])?$where[] = $search['wherearr']['num_iid']:'';
    isset($search['wherearr']['nick'])?$where[] = $search['wherearr']['nick']:'';
    isset($where)?$wherestr[] = '(' . implode(' OR ', $where) . ')':'';
    unset($where);
    if(!$refuse){
        $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
        $base_order = preg_replace("/(start|promotion_price|discount|volume)_(desc|asc)/", '`$1` $2', $_webset['base_order']);
        $base_order = !empty($base_order)?$base_order:'';
        $order = empty($order)?'`sort` desc,day desc,isrec<>1,' . $base_order:$order;
    }else{
        $countwhere = !empty($wherestr)?implode(' AND ', $wherestr):'1';
        $wherestr[] = '(b.idtype=\'goods\' or b.idtype is null)';
        $wherestr = implode(' AND ', $wherestr);
    }
    $wherestr .= ' ORDER BY ' . $order;
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    if(!$refuse){
        $query = lib_database :: rquery('select *,FROM_UNIXTIME(`start`,"%Y%m%d") as day from ' . tname('goods') . ' where ' . $wherestr);
        while ($rt = lib_database :: fetch_one()){
            $data[] = $rt;
        }
        $output = array();
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('goods') . ' where ' . $countwhere);
    }else{
        $query = lib_database :: rquery('select a.*,FROM_UNIXTIME(a.`start`,"%Y%m%d") as day,b.refuse from ' . tname('goods') . ' as a left join ' . tname('refuse') . ' as b ON a.id=b.id where ' . $wherestr);
        while ($rt = lib_database :: fetch_one()){
            $data[] = $rt;
        }
        $output = array();
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('goods') . ' as a where ' . $countwhere);
    }
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    $urls = array();
    foreach ($search['urls'] as $key => $value){
        $url = explode('=', $value);
        $urls[$url[0]] = $url[1];
    }
    $output['urls'] = $urls;
    $output['url'] = implode('&', $search['urls']);
    $output['url'] = !empty($output['url'])?'&' . $output['url']:"";
    return $output;
}
function goodAdd($good){
    global $_timestamp;
    if(!empty($good['id'])){
        lib_database :: update("goods", $good, 'id=\'' . $good['id'] . '\'');
    }else{
        $good['addtime'] = $_timestamp;
        lib_database :: insert("goods", array_keys($good), $good);
    }
}
function getgood($id){
    global $_webset;
    $good = lib_database :: get_one('select * from ' . tname('goods') . ' where id=\'' . $id . '\'');
    return $good;
}
function getiidgood($iid){
    global $_webset;
    $good = lib_database :: get_one('select * from ' . tname('goods') . ' where num_iid=\'' . $iid . '\'');
    return $good;
}
function goodUrl($iid){
    $url = lib_database :: get_one('select urls from ' . tname('urls') . ' where iid=\'' . $iid . '\'');
    if(empty($url)){
        $good = getiidgood($iid);
        if(!empty($good)){
            $taoke_info = get_taoke($iid);
            if(empty($taoke_info['click_url'])){
                return 'http://localhost/tiancheng/tiancheng/home/template/default/index/show.tpl.php?id=' . $iid;
            }
            if(!empty($taoke_info)){
                lib_database :: insert('urls', array('urls', 'iid'), array($taoke_info['click_url'], $iid));
                return $taoke_info['click_url'];
            }else{
                return 'http://item.taobao.com/item.htm?id=' . $iid;
            }
        }
        return false;
    }
    return $url['urls'];
}
function setUrl($urls){
    lib_database :: wquery('REPLACE INTO  `' . tname('urls') . '` (  `iid` ,  `urls` )VALUES (\'' . $urls['iid'] . '\',  \'' . $urls['urls'] . '\')');
}
function trylist($wherestr = array(), $order = '`sort` DESC', $start = 0, $num = 20, $refuse = false){
    global $_timestamp;
    $intkeys = array('status' => 'status');
    $strkeys = array();
    $randkeys = array();
    $likekeys = array('title' => 'keyword', 'num_iid' => 'keyword', 'nick' => 'keyword');
    if($refuse){
        $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
    }else{
        $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys, 'a.');
    }
    $type = request('type', 0);
    if(!empty($type)){
        if($type == 1){
            $where[] = 'start<' . $_timestamp . ' and end>' . $_timestamp;
        }elseif ($type == -1){
            $where[] = 'end<' . $_timestamp;
        }elseif ($type == 2){
            $where[] = 'start>' . $_timestamp;
        }
    }
    !empty($where)?$wherestr[] = '(' . implode(' AND ', $where) . ')':'';
    unset($where);
    isset($search['wherearr']['title'])?$where[] = $search['wherearr']['title']:'';
    isset($search['wherearr']['num_iid'])?$where[] = $search['wherearr']['num_iid']:'';
    isset($search['wherearr']['nick'])?$where[] = $search['wherearr']['nick']:'';
    isset($where)?$wherestr[] = '(' . implode(' OR ', $where) . ')':'';
    unset($where);
    if(!$refuse){
        $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
        $wherestr .= ' ORDER BY ' . $order;
        if ($start > -1 && $num > 0){
            $wherestr .= " LIMIT {$start}, {$num}";
        }
        $data = array();
        $query = lib_database :: rquery('select * from ' . tname('try') . ' where ' . $wherestr);
        while ($rt = lib_database :: fetch_one()){
            $data[] = $rt;
        }
        $output = array();
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('try') . ' where ' . $countwhere);
    }else{
        $countwhere = !empty($wherestr)?implode(' AND ', $wherestr):'1';
        $wherestr[] = '(b.idtype=\'try\' or b.idtype is null)';
        $wherestr = implode(' AND ', $wherestr);
        $wherestr .= ' ORDER BY ' . $order;
        if ($start > -1 && $num > 0){
            $wherestr .= " LIMIT {$start}, {$num}";
        }
        $data = array();
        $query = lib_database :: rquery('select a.*,b.refuse from ' . tname('try') . ' as a left join ' . tname('refuse') . ' as b ON a.id=b.id where ' . $wherestr);
        while ($rt = lib_database :: fetch_one()){
            $data[] = $rt;
        }
        $output = array();
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('try') . ' as a where ' . $countwhere);
    }
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    $output['url'] = implode('&', $search['urls']);
    $output['url'] = !empty($output['url'])?'&' . $output['url']:"";
    return $output;
}
function tryadd($try){
    global $_timestamp;
    if(!empty($try['id'])){
        lib_database :: update("try", $try, 'id=\'' . $try['id'] . '\'');
    }else{
        $try['addtime'] = $_timestamp;
        lib_database :: insert("try", array_keys($try), $try);
    }
}
function tryinfo($id){
    return lib_database :: get_one("select * from " . tname('try') . " where id=" . $id);
}
function tryiidinfo($iid){
    return lib_database :: get_one("select * from " . tname('try') . " where num_iid=" . $iid);
}
function register($user){
    global $access, $_webset;
    if(!lib_validate :: email($user['email'])){
        throw new Exception('邮箱格式不正确！');
        return -2;
    }
    if(check_account_exist($user['email'], 'email', 0, $user['apps'])){
        throw new Exception('邮箱被占用！');
        return -3;
    }
    if(!empty($user['mobile'])){
        if(check_account_exist($user['mobile'], 'mobile', 0, $user['apps'])){
            throw new Exception('手机被占用！');
            return -3;
        }
    }
    if(!empty($user['user_name'])){
        if(!lib_validate :: user_name($user['user_name'])){
            throw new Exception('用户名式不正确！');
            return -2;
        }
        if(check_account_exist($user['user_name'], 'user_name', $user['uid'], $user['apps'])){
            throw new Exception('用户名被占用！');
            return -3;
        }
    }
    $loginpwd = $user['userpwd'];
    if(preg_match('/[^\x00-\x80]+/', $user['userpwd']) || strlen($user['userpwd']) < 6 || strlen($user['userpwd']) > 16){
        throw new Exception('密码格式不合法！');
        return -4;
    }else{
        $user['userpwd'] = $access -> _get_encodepwd($user['userpwd']);
    }
    $uid = save_user($user);
    @$usertag = lib_request :: $cookies['usertag'];
    if(!empty($usertag)){
        $invitelog = array('usertag' => trim($usertag, '#'), 'tuser_name' => empty($user['user_name'])?$user['email']:$user['user_name'], 'tuid' => $uid);
        invitelog($invitelog);
    }
    return $uid;
}
function save_user($user = array()){
    global $_webset, $_timestamp, $_ip;
    if(!isset($user['apps']) || empty($user['apps']))$user['apps'] = APPNAME;
    if(!isset($user['groups']) || empty($user['apps']))$user['groups'] = APPNAME . '-' . APPNAME;
    $user['regtime'] = $_timestamp;
    $user['regip'] = $_ip;
    foreach ($user as $key => $value){
        if(in_array($key, array('user_name', 'email', 'mobile', 'apps', 'groups', 'sta', 'regtime', 'regip', 'logintime', 'loginip', 'userpwd'))){
            $user_main[$key] = $value;
        }else{
            $users_fields[$key] = $value;
        }
    }
    lib_database :: insert('users', array_keys($user_main), $user_main);
    $uid = lib_database :: insert_id();
    $hook_register = 'hook_' . APPNAME . '_register';
    if(function_exists($hook_register)){
        $users_fields['integral'] += intval($hook_register());
    }
    $users_fields['uid'] = $uid;
    lib_database :: insert('users_' . $user_main['apps'] . '_fields', array_keys($users_fields), $users_fields);
    return $uid;
}
function updateuser($userinfo, $apps = ''){
    global $access;
    $uid = $userinfo['uid'];
    unset($userinfo['uid']);
    if(empty($apps)){
        $apps = APPNAME;
    }
    $userpwd = $userinfo['userpwd'];
    unset($userinfo['userpwd']);
    if(!empty($userpwd)){
        $userpwd = $access -> _get_encodepwd($userpwd);
        lib_database :: update('users', array('userpwd' => $userpwd), 'uid=\'' . $uid . '\'');
    }
    if(!empty($userinfo['user_name'])){
        if(check_account_exist($userinfo['user_name'], 'user_name', $uid, $apps)){
            throw new Exception('用户名已经存在！');
            return false;
        }
    }
    if(!empty($userinfo['email'])){
        if(check_account_exist($userinfo['email'], 'email', $uid, $apps)){
            throw new Exception('邮箱已经存在！');
            return false;
        }
    }
    if(!empty($userinfo['mobile'])){
        if(check_account_exist($userinfo['mobile'], 'mobile', $uid, $apps)){
            throw new Exception('手机已经存在！');
            return false;
        }
    }
    if(!empty($userinfo)){
        $user_main = $users_fields = array();
        foreach ($userinfo as $key => $value){
            if(in_array($key, array('user_name', 'email', 'mobile', 'apps', 'groups', 'sta', 'regtime', 'regip', 'logintime', 'loginip', 'userpwd'))){
                $user_main[$key] = $value;
            }elseif (in_array($key, array('sign', 'integral', 'lastsign', 'sex', 'year', 'month', 'day', 'qq', 'alipay', 'province', 'city', 'county', 'tag'))){
                $users_fields[$key] = $value;
            }
        }
        !empty($user_main) && lib_database :: update('users', $user_main, 'uid=\'' . $uid . '\'');
        !empty($users_fields) && lib_database :: update('users_' . $apps . '_fields', $users_fields, 'uid=\'' . $uid . '\'');
        $users_fields = array_merge($users_fields, $user_main);
        lib_database :: update('users_' . $apps . '_session', $users_fields, 'uid=\'' . $uid . '\'');
    }
}
function send_register_email($email, $uid, $data){
    global $_webset, $_timestamp;
    $data['site_name'] = $_webset['site_name'];
    $data['time'] = date('Y-m-d H:i:s', $_timestamp);
    $data['email'] = $email;
    $email_tpl = unserialize($_webset['email_tpl_register']);
    if($_webset['email_open'] == 1){
        if(empty($data['url'])){
            $code = creat_code();
            $data['url'] = u('user', 'activation', array('op' => 'register', 'code' => $code));
        }else{
            $code = $data['code'];
            unset($data['code']);
        }
        $hadesend = lib_database :: get_one('select * from ' . tname('activating') . ' where type=\'register\' and email=\'' . $email . '\' and uid=' . $uid);
        if(!empty($hadesend)){
            lib_database :: update('activating', array('code' => $code, 'addtime' => $_timestamp, 'uid' => $uid), 'type=\'register\' and email=\'' . $email . '\'');
        }else{
            lib_database :: insert('activating', array('email', 'uid', 'code', 'addtime', 'type'), array($email, $uid, $code, $_timestamp, 'register'));
        }
        $tpl = get_email_tpl('register', $data);
        send_email($email, $data['site_name'] . $email_tpl['title'], $tpl);
    }else{
        register_email($email, $uid);
    }
    return true;
}
function register_email($email, $uid){
    lib_database :: update('users', array('sta' => 1), 'uid=\'' . $uid . '\' AND email=\'' . $email . '\'');
    lib_database :: delete('activating', 'uid=\'' . $uid . '\' and email=\'' . $email . '\' and type=\'register\'');
}
function sys_send_email($to, $subject, $data, $type){
    send_email($to, $subject, get_email_tpl($type, $data));
}
function get_email_tpl($type, $data = array()){
    global $_webset;
    $tpl = unserialize($_webset['email_tpl_' . $type]);
    $tpl = preg_replace("/\{(.*?)\}/", "{\$data[$1]}", $tpl['tpl']);
    eval("\$tpl = \"$tpl\";");
    return $tpl;
}
function exclist($wherestr = array(), $order = '`sort` DESC', $start = 0, $num = 20, $refuse = false){
    global $_timestamp;
    $intkeys = array('status' => 'status');
    $strkeys = array();
    $randkeys = array();
    $likekeys = array('title' => 'keyword', 'num_iid' => 'keyword', 'nick' => 'keyword');
    if($refuse){
        $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys, 'a.');
    }else{
        $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
    }
    $type = request('type', 0);
    if(!empty($type)){
        if($type == 1){
            $where[] = 'start<' . $_timestamp . ' and end>' . $_timestamp;
        }elseif ($type == -1){
            $where[] = 'end<' . $_timestamp;
        }elseif ($type == 2){
            $where[] = 'start>' . $_timestamp;
        }
    }
    !empty($where)?$wherestr[] = '(' . implode(' AND ', $where) . ')':'';
    unset($where);
    isset($search['wherearr']['title'])?$where[] = $search['wherearr']['title']:'';
    isset($search['wherearr']['num_iid'])?$where[] = $search['wherearr']['num_iid']:'';
    isset($search['wherearr']['nick'])?$where[] = $search['wherearr']['nick']:'';
    isset($where)?$wherestr[] = '(' . implode(' OR ', $where) . ')':'';
    unset($where);
    if(!$refuse){
        $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
        $wherestr .= ' ORDER BY ' . $order;
        if ($start > -1 && $num > 0){
            $wherestr .= " LIMIT {$start}, {$num}";
        }
        $data = array();
        $query = lib_database :: rquery('select * from ' . tname('exchange') . ' where ' . $wherestr);
        while ($rt = lib_database :: fetch_one()){
            $data[] = $rt;
        }
        $output = array();
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('exchange') . ' where ' . $countwhere);
    }else{
        $countwhere = !empty($wherestr)?implode(' AND ', $wherestr):'1';
        $wherestr[] = '(b.idtype=\'exchange\' or b.idtype is null)';
        $wherestr = implode(' AND ', $wherestr);
        $wherestr .= ' ORDER BY ' . $order;
        if ($start > -1 && $num > 0){
            $wherestr .= " LIMIT {$start}, {$num}";
        }
        $data = array();
        $query = lib_database :: rquery('select * from ' . tname('exchange') . ' as a left join ' . tname('refuse') . ' as b ON a.id=b.id where ' . $wherestr);
        while ($rt = lib_database :: fetch_one()){
            $data[] = $rt;
        }
        $output = array();
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('exchange') . ' as a where ' . $countwhere);
    }
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    $output['url'] = implode('&', $search['urls']);
    $output['url'] = !empty($output['url'])?'&' . $output['url']:"";
    return $output;
}
function excadd($exc){
    global $_timestamp;
    if(!empty($exc['id'])){
        lib_database :: update("exchange", $exc, 'id=\'' . $exc['id'] . '\'');
    }else{
        $exc['addtime'] = $_timestamp;
        lib_database :: insert("exchange", array_keys($exc), $exc);
    }
}
function getexc($id){
    return lib_database :: get_one('select * from ' . tname('exchange') . ' where id=\'' . $id . '\'');
}
function exciidinfo($iid){
    return lib_database :: get_one('select * from ' . tname('exchange') . ' where num_iid=\'' . $iid . '\'');
}
function gettry($id){
    return lib_database :: get_one('select * from ' . tname('try') . ' where id=' . $id);
}
function hotexc($id){
    return lib_database :: select('exchange', '*', 'id!=\'' . $id . '\' order by apply desc limit 0,4');
}
function check_account_exist($value, $type = 'email', $uid = 0, $apps = ''){
    $where = '`' . $type . '`=\'' . $value . '\'';
    if(!empty($uid)){
        $where .= ' and `uid`!=' . $uid;
    }
    if(!empty($apps)){
        $where .= ' and `apps`=\'' . $apps . '\'';
    }else{
        $where .= ' and `apps`=\'' . APPNAME . '\'';
    }
    $row = lib_database :: get_one('SELECT * from `' . tname('users') . '` where ' . $where);
    if(!empty($row)){
        return $row;
    }else{
        return false;
    }
}
function get_user_info($where, $apps){
    if(empty($apps)){
        $apps = APPNAME;
    }
    return lib_database :: get_one('select b.*,a.* from ' . tname('users') . ' as a left join ' . tname('users_' . $apps . '_fields') . ' as b ON a.uid=b.uid where ' . $where);
}
function falselogin($data){
    global $access;
    return $access -> falselogin($data);
}
function getintegral($day = "today"){
    global $user, $_webset;
    if($day == "today" && $user['lastsign'] > (strtotime('today')-3600 * 24)){
        $signday = $user['sign'] + 1;
    }elseif ($day == "tomorrow" && $user['lastsign'] > (strtotime('today'))){
        $signday = $user['sign'] + 1;
    }elseif ($day == "todayhade" && $user['lastsign'] > (strtotime('today'))){
        $signday = $user['sign'];
    }else{
        $signday = 1;
    }
    $signintegral = $_webset['reward_plus'] * ceil(($signday-1) / $_webset['reward_continuous_day']);
    $signintegral += $_webset['reward_sign_day'];
    if($signintegral > $_webset['reward_daymax']){
        $signintegral = $_webset['reward_daymax'];
    }
    return array('sign' => $signday, 'integral' => $signintegral);
}
function daysignlog($daytime){
    global $user;
    return lib_database :: get_one('select * from ' . tname('users_changelog') . ' where uid=\'' . $user['uid'] . '\' and type=\'sign\' and addtime>=\'' . $daytime . '\' and addtime<\'' . ($daytime + 3600 * 24) . '\'');
}
function sign(){
    global $user, $_timestamp;
    $signintegral = getintegral();
    if(!empty($user['uid'])){
        lib_database :: update('users_' . APPNAME . '_fields', array('sign' => $signintegral['sign'], 'integral' => $user['integral'] + $signintegral['integral'], 'lastsign' => $_timestamp), 'uid=\'' . $user['uid'] . '\'');
        lib_database :: update('users_' . APPNAME . '_session', array('sign' => $signintegral['sign'], 'integral' => $user['integral'] + $signintegral['integral'], 'lastsign' => $_timestamp), 'uid=\'' . $user['uid'] . '\'');
        changelog(array('uid' => $user['uid'], 'integ' => $signintegral['integral'], 'type' => 'sign', 'exp' => date('Y-m-d', $_timestamp) . '签到', 'addtime' => $_timestamp));
        return $signintegral;
    }
    return false;
}
function changelog($log){
    global $_timestamp, $_ip;
    $log['addtime'] = $_timestamp;
    $log['ip'] = $_ip;
    lib_database :: insert('users_changelog', array_keys($log), $log);
}
function getdaychangelog($uid, $where = array()){
    $where[] = 'uid=\'' . $uid . '\'';
    $where = implode(' and ', $where);
    $integ = lib_database :: get_one('select sum(integ) as integ from ' . tname('users_changelog') . ' where ' . $where);
    return $integ['integ'];
}
function changeloglist($wherestr = array(), $order = '`addtime` asc', $start = 0, $num = 20){
    $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
    $wherestr .= ' ORDER BY ' . $order;
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    $query = $query = lib_database :: rquery('select * from ' . tname('users_changelog') . ' where ' . $wherestr);
    while ($rt = lib_database :: fetch_one()){
        $data[] = $rt;
    }
    $output = array();
    $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('users_changelog') . ' where ' . $countwhere);
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    return $output;
}
function saveaddress($address){
    $ishadeaddress = useraddress($address['uid']);
    if(!empty($ishadeaddress)){
        lib_database :: update('users_addr', $address, 'uid=\'' . $address['uid'] . '\'');
    }else{
        lib_database :: insert('users_addr', array_keys($address), $address);
    }
}
function useraddress($uid){
    return lib_database :: get_one('select * from ' . tname('users_addr') . ' where uid=\'' . $uid . '\'');
}
function check_set_pwd(){
    global $user;
    $user_pwd = lib_database :: get_one('select userpwd from ' . tname('users') . ' where uid=\'' . $user['uid'] . '\'');
    if(empty($user_pwd['userpwd'])){
        return false;
    }else{
        return $user_pwd['userpwd'];
    }
}
function getarticle($id){
    return lib_database :: get_one('select * from ' . tname('article') . ' where id=\'' . $id . '\'');
}
function comment(){
    global $user, $_timestamp, $_ip, $_webset;
    if(empty($user['uid'])){
        $gourl = request('gourl', '');
        $url = u('user', 'login', array('gourl' => base64_encode($gourl)));
        message('-13', '操作提示', '请登陆', $url);
    }
    $comment = request('comment', array());
    if(empty($comment) || empty($comment['message'])){
        message('-1', '操作提示', '请填写评论');
    }
    $comment['addtime'] = $_timestamp;
    $comment['user_name'] = show_user_name($user['user_name'], $user['email']);
    $comment['uid'] = $user['uid'];
    $comment['ip'] = $_ip;
    $comment['message'] = filterwords($comment['message']);
    if(!$comment['message']){
        message('-1', '系统提示', '您的发言存在敏感词');
    }
    if($comment['idtype'] == 'try'){
        $try = tryinfo($comment['id']);
        if(empty($try)){
            message('-1', '申请提示', '试用不存在');
        }
        if($try['needintegral'] > $user['integral']){
            message('-1', '申请提示', '你的积分不够');
        }elseif($try['end'] < $_timestamp || $try['start'] > $_timestamp || $try['payment'] >= $try['num']){
            message('-1', '申请提示', '试用不能申请');
        }
        lib_database :: update('try', array('apply' => $try['apply'] + 1), 'id=\'' . $try['id'] . '\'');
        if($try['needintegral'] > 0){
            lib_database :: update('users_home_fields', array('integral' => $user['integral'] - $try['needintegral']), 'uid=\'' . $user['uid'] . '\'');
            lib_database :: update('users_home_session', array('integral' => $user['integral'] - $try['needintegral']), 'uid=\'' . $user['uid'] . '\'');
            changelog(array('uid' => $user['uid'], 'integ' => - $try['needintegral'], 'type' => 'try', 'exp' => "申请试用" . $try['title'], 'addtime' => $_timestamp, 'ip' => $_ip));
        }
        lib_database :: insert('comment', array_keys($comment), $comment);
        $cid = lib_database :: insert_id();
        applylogto(array('id' => $try['id'], 'idtype' => 'try', 'title' => $try['title'], 'uid' => $user['uid'], 'user_name' => $user['user_name'], 'integration' => $try['needintegral'], 'addtime' => $_timestamp, 'status' => 0, 'cid' => $cid));
        message('0', '试用申请', '试用申请成功');
    }elseif($comment['idtype'] == 'exchange'){
        $exchange = getexc($comment['id']);
        if(empty($exchange)){
            message('-1', '兑换提示', '兑换不存在');
        }
        if($exchange['needintegral'] > $user['integral']){
            message('-1', '兑换提示', '你的积分不够');
        }elseif($exchange['end'] < $_timestamp || $exchange['start'] > $_timestamp || $exchange['apply'] >= $exchange['num']){
            message('-1', '兑换提示', '礼品已兑换完了');
        }
        lib_database :: update('exchange', array('apply' => $exchange['apply'] + 1), 'id=\'' . $exchange['id'] . '\'');
        if($exchange['needintegral'] > 0){
            lib_database :: update('users_home_fields', array('integral' => $user['integral'] - $exchange['needintegral']), 'uid=\'' . $user['uid'] . '\'');
            lib_database :: update('users_home_session', array('integral' => $user['integral'] - $exchange['needintegral']), 'uid=\'' . $user['uid'] . '\'');
            changelog(array('uid' => $user['uid'], 'integ' => - $exchange['needintegral'], 'type' => 'exchange', 'exp' => "积分兑换" . $exchange['title'], 'addtime' => $_timestamp, 'ip' => $_ip));
        }
        lib_database :: insert('comment', array_keys($comment), $comment);
        $cid = lib_database :: insert_id();
        applylogto(array('id' => $exchange['id'], 'idtype' => 'exchange', 'title' => $exchange['title'], 'uid' => $user['uid'], 'user_name' => $user['user_name'], 'integration' => $exchange['needintegral'], 'addtime' => $_timestamp, 'status' => 0, 'cid' => $cid));
        message('0', '兑换提示', '兑换成功，等待处理');
    }elseif($comment['idtype'] == 'sun'){
        $applylog = getapplylog($comment['id']);
        if(empty($applylog) || $applylog['status'] < 2){
            message('-1', '晒单提示', '操作错误');
        }
        if($applylog['status'] > 2){
            message('-1', '晒单提示', '你已经晒单成功了');
            exit();
        }
        $sunpic = request('pic', array());
        $comment['message'] .= getsunpichtml($sunpic);
        lib_database :: update('applylog', array('status' => 3), 'aid=\'' . $applylog['aid'] . '\'');
        if($_webset['reward_showsingle'] > 0){
            lib_database :: update('users_home_fields', array('integral' => $user['integral'] + $_webset['reward_showsingle']), 'uid=\'' . $user['uid'] . '\'');
            lib_database :: update('users_home_session', array('integral' => $user['integral'] + $_webset['reward_showsingle']), 'uid=\'' . $user['uid'] . '\'');
            changelog(array('uid' => $user['uid'], 'integ' => + $_webset['reward_showsingle'], 'type' => 'sun', 'exp' => "晒单" . $applylog['title'] . '奖励', 'addtime' => $_timestamp, 'ip' => $_ip));
        }
        lib_database :: insert('comment', array_keys($comment), $comment);
        $cid = lib_database :: insert_id();
        message('0', '晒单提示', '晒单成功');
    }else{
        lib_database :: insert('comment', array_keys($comment), $comment);
        if($_webset['reward_comment'] > 0 && $_webset['base_isComment'] == 1 && $_webset['base_commentAudit'] == -1){
            if($_webset['reward_comment_daymax'] > 0){
                $reward_comment = getdaychangelog($comment['uid'], array('type=\'comment\'', 'addtime>' . strtotime("today"), 'addtime<' . strtotime("tomorrow")));
            }
            if($_webset['reward_comment_daymax'] <= 0 || $reward_comment < $_webset['reward_comment_daymax']){
                lib_database :: update('users_home_fields', array('integral' => $user['integral'] + $_webset['reward_comment']), 'uid=\'' . $user['uid'] . '\'');
                lib_database :: update('users_home_session', array('integral' => $user['integral'] + $_webset['reward_comment']), 'uid=\'' . $user['uid'] . '\'');
                changelog(array('uid' => $user['uid'], 'integ' => + $_webset['reward_comment'], 'type' => 'comment', 'exp' => '评论奖励', 'addtime' => $_timestamp, 'ip' => $_ip));
            }
        }
        $comment['avatar'] = avatar($user['uid'], 'little');
        message('0', '评论提示', '评论成功', '', $comment);
    }
}
function reward_user_perfect($userinfo){
    global $user, $_webset;
    if(!empty($userinfo['sex']) && !empty($userinfo['year']) && !empty($userinfo['month']) && !empty($userinfo['day']) && !empty($userinfo['qq']) && !empty($userinfo['alipay']) && !empty($userinfo['province']) && !empty($userinfo['city']) && !empty($userinfo['county']) && empty($user['perfect'])){
        $reward_user_perfect = intval($_webset['reward_user_perfect']);
        if(!empty($reward_user_perfect)){
            lib_database :: wquery('update ' . tname('users_home_fields') . ' set `integral`=`integral`+' . abs($reward_user_perfect) . ',`perfect`=1 where `uid`=' . $user['uid'] . ' and `perfect`=0');
            lib_database :: wquery('update ' . tname('users_home_session') . ' set `integral`=`integral`+' . abs($reward_user_perfect) . ',`perfect`=1 where `uid`=' . $user['uid'] . ' and `perfect`=0');
            changelog(array('uid' => $user['uid'], 'integ' => $reward_user_perfect, 'type' => 'reward', 'exp' => '信息完善奖励'));
        }
    }
}
function reward_quick_login($quick){
    global $user, $_webset;
    $reward_quick_login = intval($_webset['reward_quick_login']);
    $quick_arr = explode('_', $user['quick']);
    if(!empty($reward_quick_login) && !in_array($quick, $quick_arr)){
        $quick_arr[] = $quick;
        $quick = implode('_', $quick_arr);
        lib_database :: wquery('update ' . tname('users_home_fields') . ' set `integral`=`integral`+' . abs($reward_quick_login) . ',`quick`=\'' . $quick . '\' where `uid`=' . $uid);
        lib_database :: wquery('update ' . tname('users_home_session') . ' set `integral`=`integral`+' . abs($reward_quick_login) . ',`quick`=\'' . $quick . '\' where `uid`=' . $uid);
        changelog(array('uid' => $user['uid'], 'integ' => $reward_quick_login, 'type' => 'reward', 'exp' => '关联' . $quick . '账号奖励'));
    }
}
function reward_auth_email($user){
    global $_webset;
    $reward_auth_email = intval($_webset['reward_auth_email']);
    if(!empty($reward_auth_email)){
        lib_database :: wquery('update ' . tname('users') . ' set `sta`=1,`email`=\'' . $user['email'] . '\' where `uid`=' . $user['uid']);
        lib_database :: wquery('update ' . tname('users_home_fields') . ' set `integral`=`integral`+' . abs($reward_auth_email) . ' where `uid`=' . $user['uid']);
        lib_database :: wquery('update ' . tname('users_home_session') . ' set `integral`=`integral`+' . abs($reward_auth_email) . ',`sta`=1,`email`=\'' . $user['email'] . '\' where `uid`=' . $user['uid']);
        changelog(array('uid' => $user['uid'], 'integ' => $reward_auth_email, 'type' => 'reward', 'exp' => '验证邮箱'));
    }else{
        lib_database :: wquery('update ' . tname('users') . ' set `sta`=1,`email`=\'' . $user['email'] . '\' where `uid`=' . $user['uid']);
        lib_database :: wquery('update ' . tname('users_home_session') . ' set `sta`=1,`email`=\'' . $user['email'] . '\' where `uid`=' . $user['uid']);
    }
}
function getcomment($wherestr = array(), $order = '`addtime` DESC', $start = 0, $num = CPAGE){
    $intkeys = array();
    $strkeys = array('idtype' => 'idtype');
    $randkeys = array();
    $likekeys = array('message' => 'keyword', 'user_name' => 'keyword', 'author' => 'keyword');
    $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
    !empty($search['wherearr']['idtype'])?$where[] = $search['wherearr']['idtype']:'';
    !empty($where)?$wherestr[] = '(' . implode(' AND ', $where) . ')':'';
    unset($where);
    isset($search['wherearr']['message'])?$where[] = $search['wherearr']['message']:'';
    isset($search['wherearr']['user_name'])?$where[] = $search['wherearr']['user_name']:'';
    isset($search['wherearr']['author'])?$where[] = $search['wherearr']['author']:'';
    isset($where)?$wherestr[] = '(' . implode(' OR ', $where) . ')':'';
    unset($where);
    $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
    $wherestr .= ' ORDER BY ' . $order;
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    $query = lib_database :: rquery('select * from ' . tname('comment') . ' where ' . $wherestr);
    while ($rt = lib_database :: fetch_one()){
        $data[] = $rt;
    }
    $output = array();
    $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('comment') . ' where ' . $countwhere);
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    $output['urls'] = $search['urls'];
    $output['url'] = implode('&', $search['urls']);
    $output['url'] = !empty($output['url'])?'&' . $output['url']:"";
    return $output;
}
function commentlist($id, $type = 'goods'){
    global $_webset;
    $where = array('id=\'' . $id . '\'', 'idtype=\'' . $type . '\'');
    if($type == 'goods' && $_webset['base_commentAudit'] == 1){
        $where[] = 'status=1';
    }
    $start = intval(request('start', 0));
    $result = getcomment($where, '`addtime` DESC', $start, CPAGE);
    return $result;
}
function getsuncomment($wherestr = array(), $order = '`addtime` DESC', $start = 0, $num = CPAGE){
    $wherestr[] = 'b.idtype=\'sun\'';
    $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
    $wherestr .= ' ORDER BY ' . $order;
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    $query = lib_database :: rquery('select b.* from ' . tname('applylog') . ' as a left join ' . tname('comment') . ' as b on a.aid=b.id where ' . $wherestr);
    while ($rt = lib_database :: fetch_one()){
        $data[] = $rt;
    }
    $output = array();
    $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('applylog') . ' as a left join ' . tname('comment') . ' as b on a.aid=b.id where ' . $countwhere);
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    $output['url'] = !empty($output['url'])?'&' . $output['url']:"";
    return $output;
}
function suncommentlist($id, $type = 'try'){
    $start = intval(request('start', 0));
    $result = getsuncomment(array('a.id=\'' . $id . '\'', 'a.idtype=\'' . $type . '\''), 'b.`addtime` DESC', $start, CPAGE);
    return $result;
}
function goodsfav(){
    global $user, $_timestamp;
    if(empty($user['uid'])){
        $gourl = request('gourl', '');
        if(!empty($gourl)){
            $url = u('user', 'login', array('gourl' => base64_encode($gourl)));
        }else{
            $url = u('user', 'login');
        }
        message('-13', '收藏提示', '请登陆', $url);
    }
    $id = intval(request('id'));
    if(!empty($id)){
        $is_fav = lib_database :: get_rows_num('users_fav', 'gid=\'' . $id . '\' and uid=\'' . $user['uid'] . '\'');
        if(!empty($is_fav)){
            message('0', '收藏提示', '已经收藏');
        }
        lib_database :: insert('users_fav', array('uid', 'gid', 'addtime'), array($user['uid'], $id, $_timestamp));
        lib_database :: wquery('update ' . tname('goods') . ' set `fav`=`fav`+1 where id=\'' . $id . '\'');
        message('0', '收藏提示', '收藏成功');
    }
    message('0', '收藏提示', '操作失败');
}
function goodsfavlist($wherestr = array(), $order = '`time` DESC', $start = 0, $num = 20){
    global $_timestamp, $_webset;
    $wherestr[] = 'b.id IS NOT NULL';
    $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
    $wherestr .= ' ORDER BY ' . $order;
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    $query = lib_database :: rquery('select a.*,b.title,b.start,b.end,b.status,b.issteal,b.promotion_price,b.num_iid,b.pic,b.taopic from ' . tname('users_fav') . ' as a left join ' . tname('goods') . ' as b on a.gid=b.id where ' . $wherestr);
    while ($rt = lib_database :: fetch_one()){
        $data[] = $rt;
    }
    $output = array();
    $total = lib_database :: rquery('SELECT COUNT(a.flog) AS rows from ' . tname('users_fav') . ' as a left join ' . tname('goods') . ' as b on a.gid=b.id where ' . $countwhere);
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    return $output;
}
function goodsfavloglist($wherestr = array(), $order = '`time` DESC', $start = 0, $num = 20, $page = false){
    global $_timestamp;
    $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
    $wherestr .= ' ORDER BY ' . $order;
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    $query = lib_database :: rquery('select * from ' . tname('users_fav') . ' where ' . $wherestr);
    while ($rt = lib_database :: fetch_one()){
        $data[] = $rt;
    }
    $output = array();
    if($page){
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('users_fav') . ' where ' . $countwhere);
        $total = lib_database :: fetch_one();
        $output['total'] = $total['rows'];
    }
    $output['data'] = $data;
    return $output;
}
function invitelog($log){
    global $_timestamp, $_ip;
    $log['ip'] = $_ip;
    $log['addtime'] = $_timestamp;
    $log['reward'] = reward_invite($log['usertag']);
    lib_database :: insert('users_invitelog', array_keys($log), $log);
    if($log['reward'] > 0){
        $rewarduser = lib_database :: get_one('select uid,integral from ' . tname('users_' . APPNAME . '_fields') . ' where tag=\'' . $log['usertag'] . '\'');
        lib_database :: update('users_' . APPNAME . '_fields', array('integral' => $rewarduser['integral'] + $log['reward']), 'uid=\'' . $rewarduser['uid'] . '\'');
        changelog(array('uid' => $rewarduser['uid'], 'integ' => $log['reward'], 'type' => 'reward', 'exp' => '邀请好友赠送'));
    }
    setcookie("usertag", "", $_timestamp-3600);
}
function reward_invite($usertag){
    global $_webset;
    $reward = lib_database :: get_one('select sum(`reward`) as reward from ' . tname('users_invitelog') . ' where addtime>' . strtotime("today") . ' and addtime<' . strtotime('tomorrow') . ' and usertag=\'' . $usertag . '\'');
    if($reward['reward'] >= $_webset['reward_invite_daymax'] && !empty($_webset['reward_invite_daymax'])){
        return 0;
    }else{
        return $_webset['reward_invite'];
    }
}
function setusettag(){
    global $user;
    if(!empty($user['uid']) && empty($user['tag'])){
        $user['tag'] = shortstr($user['uid']);
        updateuser(array('tag' => $user['tag'], 'uid' => $user['uid']));
    }
    return $user['tag'];
}
function getinviteurl(){
    global $_webset;
    $tag = setusettag();
    return $_webset['site_url'] . '/#' . $tag;
}
function inviteloglist($wherestr = array(), $order = '`time` DESC', $start = 0, $num = 20){
    global $_timestamp;
    $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
    $wherestr .= ' ORDER BY ' . $order;
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    $query = lib_database :: rquery('select * from ' . tname('users_invitelog') . ' where ' . $wherestr);
    while ($rt = lib_database :: fetch_one()){
        $data[] = $rt;
    }
    $output = array();
    $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('users_invitelog') . ' where ' . $countwhere);
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    return $output;
}
function webtitle(){
    global $_webset, $_seo;
    if(isset($_seo[MODNAME . '_' . ACTNAME]) && !empty($_seo[MODNAME . '_' . ACTNAME])){
        $title = $_seo[MODNAME . '_' . ACTNAME]['title'];
        $keyword = $_seo[MODNAME . '_' . ACTNAME]['keyword'];
        $desc = $_seo[MODNAME . '_' . ACTNAME]['desc'];
        $globalval = array();
        if(!empty($title)){
            preg_match_all("/\{(.*?)(\[.*\])?\}/", $title, $titlevar);
            $title = '"' . preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "\".\\1.\"", $title) . '"';
            if(!empty($titlevar[1])){
                $globalval = array_merge($globalval, $titlevar[1]);
            }
        }
        if(!empty($keyword)){
            preg_match_all("/\{(.*?)(\[.*\])?\}/", $keyword, $keywordvar);
            $keyword = '"' . preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "\".\\1.\"", $keyword) . '"';
            if(!empty($keywordvar[1])){
                $globalval = array_merge($globalval, $keywordvar[1]);
            }
        }
        if(!empty($desc)){
            preg_match_all("/\{(.*?)(\[.*\])?\}/", $desc, $descvar);
            $desc = '"' . preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "\".\\1.\"", $desc) . '"';
            if(!empty($descvar[1])){
                $globalval = array_merge($globalval, $descvar[1]);
            }
        }
        if(!empty($globalval)){
            $globalval = array_unique($globalval);
            $globalval = implode(',', $globalval);
            eval("global $globalval;");
        }
        !empty($title)?eval('$_webset[\'site_title\']=' . $title . ';'):$_webset['site_title'] = '';
        !empty($keyword)?eval('$_webset[\'site_metakeyword\']=' . $keyword . ';'):$_webset['site_metakeyword'] = '';
        !empty($desc)?eval('$_webset[\'site_metadescrip\']=' . $desc . ';'):$_webset['site_metadescrip'] = '';
    }
}
function readval($val){
    return $val;
}
function goodssort($sort = ''){
    if($sort == 'new'){
        return '`start` desc';
    }elseif ($sort == 'hot'){
        return '`fav` desc';
    }else{
        return '';
    }
}
function setavatar(){
    global $user;
    if(!empty($user['uid'])){
        $picname = lib_request :: $files['avatar']['name'];
        $picsize = lib_request :: $files['avatar']['size'];
        if ($picname != ""){
            if ($picsize > 1024000){
                echo json_encode(array('code' => '-1', 'title' => '头像设置', 'msg' => '图片大小不能超过1M'));
                exit();
            }
            $type = get_extension($picname);
            if ($type != "gif" && $type != "jpg" && $type != "jpeg" && $type != "png"){
                echo json_encode(array('code' => '-1', 'title' => '头像设置', 'msg' => '图片格式不对'));
                exit();
            }
            $path = showUserPic($user['uid']);
            if(!file_exists(PATH_ROOT . '/' . $path)){
                creatFolder(PATH_ROOT . '/' . $path);
            }
            $pic_url_path = $path . 'avatar' . $type;
            $pic_path = PATH_ROOT . '/' . $pic_url_path;
            move_uploaded_file(lib_request :: $files['avatar']['tmp_name'], $pic_path);
            makeThumbnail($pic_path, PATH_ROOT . '/' . $path . 'user.normal.gif', 120, 120);
            $pic_normal_url_path = $path . 'user.normal.gif';
            makeThumbnail($pic_path, PATH_ROOT . '/' . $path . 'user.small.gif', 80, 80);
            $pic_small_url_path = $path . 'user.small.gif';
            makeThumbnail($pic_path, PATH_ROOT . '/' . $path . 'user.little.gif', 50, 50);
            $pic_little_url_path = $path . 'user.little.gif';
        }
        $avatar = array('name' => $picname, 'pic' => $pic_url_path, 'normal' => $pic_normal_url_path, 'small' => $pic_small_url_path, 'little' => $pic_little_url_path, 'size' => $picsize,);
        echo json_encode(array('code' => '0', 'title' => '头像设置', 'msg' => '头像设置成功', 'data' => $avatar));
        exit();
    }
    echo json_encode(array('code' => '-1', 'title' => '头像设置', 'msg' => '头像设置失败'));
    exit();
}
function showUserPic($uid){
    $uid = sprintf("%05d", $uid);
    $dir1 = substr($uid, 0, -4);
    $dir2 = substr($uid, -4, 2);
    $dir3 = substr($uid, -2, 2);
    $dir = 'data/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
    return $dir;
}
function avatar($uid, $type = 'normal'){
    $path = showUserPic($uid);
    $avatar = $path . 'user.' . $type . '.gif';
    if(!file_exists(PATH_ROOT . '/' . $avatar)){
        $avatar = 'data/avatar/user.' . $type . '.gif';
    }
    return $avatar;
}
function ajaxsunpic(){
    global $user;
    if(empty($user['uid'])){
        echo json_encode(array('code' => '-1', 'title' => '操作错误', 'msg' => '请您登陆'));
        exit();
    }
    foreach (lib_request :: $files as $filename => $value){
        $picname = lib_request :: $files[$filename]['name'];
        $picsize = lib_request :: $files[$filename]['size'];
        if ($picname != ""){
            if ($picsize > 1024000){
                echo json_encode(array('code' => '-1', 'title' => '晒单图片上传', 'msg' => '图片大小不能超过1M'));
                exit();
            }
            $type = get_extension($picname);
            if ($type != "gif" && $type != "jpg" && $type != "jpeg" && $type != "png"){
                echo json_encode(array('code' => '-1', 'title' => '晒单图片上传', 'msg' => '图片格式不对'));
                exit();
            }
            $rand = rand(100, 999);
            $pics = date("YmdHis") . $rand . '_' . $user['uid'] . '.' . $type;
            $dir = date('/Y/m/d/H/');
            $pic_path = PATH_UPLOAD . $dir;
            $pic_url = $pic_path . $pics;
            if(!file_exists(PATH_ROOT . $pic_path)){
                creatFolder(PATH_ROOT . $pic_path);
            }
            $pic_path = PATH_ROOT . $pic_url;
            move_uploaded_file(lib_request :: $files[$filename]['tmp_name'], $pic_path);
            makeThumbnail($pic_path, $pic_path . '.normal', 600, 600);
            $pic_normal_url_path = $pic_url . '.normal';
            makeThumbnail($pic_path, $pic_path . '.small', 100, 100);
            $pic_small_url_path = $pic_url . '.small';
        }
        $arr[$filename] = array('name' => $picname, 'filename' => trim($pics, '.' . $type), 'pic' => $pic_url, 'normal' => $pic_normal_url_path, 'small' => $pic_small_url_path, 'size' => $picsize,);
        echo json_encode(array('code' => '0', 'title' => '晒单图片上传', 'msg' => '图片上传成功', 'data' => $arr));
        exit();
    }
    echo json_encode(array('code' => '-1', 'title' => '晒单图片上传', 'msg' => '图片上传失败'));
    exit();
}
function delsunpic(){
    global $user;
    if(empty($user['uid'])){
        message('-1', '系统提示', '请您登陆');
    }
    $path = request('path');
    if(!empty($path)){
        $pathinfo = pathinfo($path);
        $pos = strpos($pathinfo['dirname'], PATH_UPLOAD . '/');
        if ($pos === false){
            message('-1', '系统提示', '删除失败失败');
        }
        $pos = strpos($pathinfo['filename'], '_' . $user['uid']);
        if ($pos === false){
            message('-1', '系统提示', '删除失败失败');
        }
        unlink(PATH_ROOT . '/' . $path);
        @unlink(PATH_ROOT . '/' . $path . '.normal');
        @unlink(PATH_ROOT . '/' . $path . '.small');
        message('0', '系统提示', '图片删除成功', '', array('filename' => $pathinfo['filename']));
    }
    message('-1', '系统提示', '删除失败失败');
}
function send_bind_email($email, $uid, $data){
    global $_webset, $_timestamp;
    $data['site_name'] = $_webset['site_name'];
    $data['time'] = date('Y-m-d H:i:s', $_timestamp);
    $data['email'] = $email;
    $email_tpl = unserialize($_webset['email_tpl_bind']);
    if($_webset['email_open'] == 1){
        if(empty($data['url'])){
            $code = creat_code();
            $data['url'] = u('user', 'activation', array('op' => 'bind', 'code' => $code));
        }else{
            $code = $data['code'];
            unset($data['code']);
        }
        $hadesend = lib_database :: get_one('select * from ' . tname('activating') . ' where type=\'bind\' and email=\'' . $email . '\'');
        if(!empty($hadesend)){
            lib_database :: update('activating', array('code' => $code, 'addtime' => $_timestamp, 'uid' => $uid), 'type=\'bind\' and email=\'' . $email . '\'');
        }else{
            lib_database :: insert('activating', array('email', 'uid', 'code', 'addtime', 'type'), array($email, $uid, $code, $_timestamp, 'bind'));
        }
        $tpl = get_email_tpl('bind', $data);
        send_email($email, $data['site_name'] . $email_tpl['title'], $tpl);
    }else{
        bind_email($email, $uid);
    }
    return true;
}
function bind_email($email, $uid){
    reward_auth_email(array('email' => $email, 'sta' => 1, 'uid' => $uid));
    lib_database :: delete('activating', 'uid=\'' . $uid . '\' and email=\'' . $email . '\' and type=\'bind\'');
    message('0', '邮箱绑定', '邮箱绑定成功', u('user', 'base'));
}
function bind_account($uid){
    $query = lib_database :: rquery('select * from ' . tname('users_token') . ' where uid=\'' . $uid . '\'');
    $data = array();
    while ($rt = lib_database :: fetch_one()){
        $data[$rt['api']] = $rt;
    }
    return $data;
}
function unbind($site){
    global $user;
    lib_database :: delete('users_token', 'uid=\'' . $user['uid'] . '\' and api=\'' . $site . '\'');
}
function bind($data){
    global $user;
    $row = lib_database :: get_one('select * from ' . tname('users_token') . ' where hash=\'' . $data['hash'] . '\'');
    if(empty($row['uid'])){
        lib_database :: insert('users_token', array('uid', 'name', 'token', 'api', 'apiuid', 'hash'), array($user['uid'], $data['user_name'], $data['token'], $data['api'], $data['apiuid'], $data['hash']));
    }else{
        if($row['uid'] == $user['uid']){
            lib_database :: update('users_token', array('token' => $data['token'], 'name' => $data['user_name']), 'hash=\'' . $data['hash'] . '\'');
        }else{
            return false;
        }
    }
    return true;
}
function delfav(){
    global $user;
    if(empty($user['uid'])){
        $url = u('user', 'login', array('gourl' => base64_encode(u('user', 'fav'))));
        message('-1', '删除失败', '请您先登录', $url);
    }
    $fav = request('fav');
    if(!empty($fav)){
        lib_database :: delete('users_fav', 'flog in (' . $fav . ') and uid=\'' . $user['uid'] . '\'');
    }
    message('0', '操作成功', '收藏已经删除');
}
function applylogto($log){
    lib_database :: insert('applylog', array_keys($log), $log);
}
function getapplylog($id){
    return lib_database :: get_one('select * from ' . tname('applylog') . ' where aid=\'' . $id . '\'');
}
function applylog($wherestr = array(), $order = 'a.`addtime` asc', $start = 0, $num = 20, $comment = true){
    $intkeys = array('status' => 'status');
    $strkeys = array();
    $randkeys = array();
    $likekeys = array('user_name' => 'keyword', 'title' => 'keyword');
    $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys, 'a.');
    isset($search['wherearr']['status'])?$where[] = $search['wherearr']['status']:'';
    !empty($where)?$wherestr[] = '(' . implode(' AND ', $where) . ')':'';
    unset($where);
    isset($search['wherearr']['user_name'])?$where[] = $search['wherearr']['user_name']:'';
    isset($search['wherearr']['title'])?$where[] = $search['wherearr']['title']:'';
    if($comment){
        $likekeysb = array('message' => 'keyword');
        $searchb = getwheres(array(), array(), array(), $likekeysb, 'b.');
        isset($search['wherearr']['message'])?$where[] = $search['wherearr']['message']:'';
    }
    isset($where)?$wherestr[] = '(' . implode(' OR ', $where) . ')':'';
    unset($where);
    $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
    $wherestr .= ' ORDER BY ' . $order;
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    if($comment){
        $query = lib_database :: rquery('select a.*,b.message from ' . tname('applylog') . ' as a left join ' . tname('comment') . ' as b on a.cid=b.cid where ' . $wherestr);
    }else{
        $query = lib_database :: rquery('select a.* from ' . tname('applylog') . ' as a where ' . $wherestr);
    } while ($rt = lib_database :: fetch_one()){
        $data[] = $rt;
    }
    $output = array();
    if($comment){
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('applylog') . ' as a left join ' . tname('comment') . ' as b on a.cid=b.cid where ' . $countwhere);
    }else{
        $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('applylog') . ' as a where ' . $countwhere);
    }
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    $output['url'] = implode('&', $search['urls']);
    $output['url'] = !empty($output['url'])?'&' . $output['url']:"";
    return $output;
}
function delgiftlog(){
    global $user;
    $aid = request('aid');
    if(empty($aid)){
        message('-1', '操作失败', '操作错误');
    }
    if(empty($user['uid'])){
        $url = u('user', 'login');
        message('-13', '操作提示', '请登陆', $url);
    }
    lib_database :: delete('applylog', 'aid=\'' . $aid . '\' and uid=\'' . $user['uid'] . '\'');
    message('0', '操作提示', '记录删除成功', '', array('aid' => $aid));
}
function activatinglog($code){
    if(lib_validate :: email($code)){
        return lib_database :: get_one('select * from ' . tname('activating') . ' where email=\'' . $code . '\'');
    }else{
        return lib_database :: get_one('select * from ' . tname('activating') . ' where code=\'' . $code . '\'');
    }
}
function againemail($email, $type){
    $log = lib_database :: get_one('select * from ' . tname('activating') . ' where email=\'' . $email . '\' and type=\'' . $type . '\'');
    if(empty($log)){
        message('-1', '操作提示', '操作错误');
    }
    if($type == 'forget' && send_forget_email($email, $log['uid'], array('user_name' => $email))){
        message('0', '操作提示', '发送成功哦，立即去找回');
    }
    if($type == 'bind' && send_bind_email($email, $log['uid'], array('user_name' => $email))){
        message('0', '操作提示', '发送成功哦，立即去激活绑定');
    }
    if($type == 'register' && send_register_email($email, $log['uid'], array('user_name' => $email))){
        message('0', '操作提示', '发送成功哦，立即去验证');
    }
    message('-1', '操作提示', '操作错误');
}
function send_forget_email($email, $uid, $data){
    global $_webset, $_timestamp;
    $data['site_name'] = $_webset['site_name'];
    $data['time'] = date('Y-m-d H:i:s', $_timestamp);
    $data['email'] = $email;
    $email_tpl = unserialize($_webset['email_tpl_forget']);
    if($_webset['email_open'] == 1 && $email_tpl['open'] == 1){
        if(empty($data['url'])){
            $code = creat_code();
            $data['url'] = u('user', 'activation', array('op' => 'forget', 'code' => $code));
        }else{
            $code = $data['code'];
            unset($data['code']);
        }
        $hadesend = lib_database :: get_one('select * from ' . tname('activating') . ' where type=\'forget\' and email=\'' . $email . '\'');
        if(!empty($hadesend)){
            lib_database :: update('activating', array('code' => $code, 'addtime' => $_timestamp, 'uid' => $uid), 'type=\'forget\' and email=\'' . $email . '\'');
        }else{
            lib_database :: insert('activating', array('email', 'uid', 'code', 'addtime', 'type'), array($email, $uid, $code, $_timestamp, 'forget'));
        }
        $tpl = get_email_tpl('forget', $data);
        send_email($email, $data['site_name'] . $email_tpl['title'], $tpl);
    }
    return true;
}
function email_message($code, $title = '', $msg = '', $gourl = '', $data = '', $limittime = 3000){
    global $_isajax, $_webset, $user;
    if($title == '') $title = '系统提示信息';
    $jumpmsg = $jstmp = '';
    if($gourl == 'javascript:;'){
        $gourl == 'location.href=';
    }
    if($gourl == '-1'){
        $gourl = "location.href=history.go(-1);";
    }
    require PATH_TPL . '/public/email_message.tpl';
    exit();
}
function getsunpichtml($sunpic){
    ob_start();
    require PATH_TPL . '/public/sunpic.tpl';
    $tpl = ob_get_contents();
    ob_end_clean();
    return $tpl;
}
function checkblack($nick){
    $black = lib_database :: get_one('select * from ' . tname('blacklist') . ' where nick=\'' . $nick . '\'');
    return $black;
}
function gethelp(){
    $helparticle = get_cache('help', 'article');
    if(empty($help)){
        $helpcat = gethelpcat();
        foreach ($helpcat as $key => $value){
            $help = array();
            lib_database :: rquery('select * from ' . tname('article') . ' where cid=\'' . $value['id'] . '\'');
            while ($rt = lib_database :: fetch_one()){
                $help[$rt['id']] = $rt;
            }
            $helparticle[$value['id']] = $help;
            set_cache('help', 'article', $helparticle);
        }
    }
    return $helparticle;
}
function gethelpcat(){
    $helpcat = get_cache('help', 'cat');
    if(empty($helpcat)){
        $helpcat = getCatList('article', 2);
        set_cache('help', 'cat', $helpcat);
    }
    return $helpcat;
}
function getarticlecat(){
    $articlecat = get_cache('article', 'cat');
    if(empty($articlecat)){
        $articlecat = getCatList('article');
        set_cache('article', 'cat', $articlecat);
    }
    return $articlecat;
}
function getgoodscat(){
    $goodscat = get_cache('goods', 'cat');
    if(empty($goodscat)){
        $goodscat = getCatList('goods');
        set_cache('goods', 'cat', $goodscat);
    }
    return $goodscat;
}
function check_nosun($type = 'try'){
    global $user;
    lib_database :: rquery('select count(*) as rows from ' . tname('applylog') . ' where uid=\'' . $user['uid'] . '\' and idtype=\'' . $type . '\' and status=2');
    $total = lib_database :: fetch_one();
    return $total['rows'];
}
function report(){
    global $_webset, $user, $_timestamp;
    if(!empty($_webset['report_login']) && empty($user['uid'])){
        $gourl = request('gourl');
        message('-3', '操作提示', '请先登录', u('user', 'login', array('gourl' => base64_encode($gourl))));
    }
    !isset($user['uid']) && $user['uid'] = 0;
    $report = request('report');
    if(empty($report['gid']) || empty($report['good'])){
        message('-1', '操作提示', '非法操作');
    }
    if(empty($report['report'])){
        message('-2', '操作提示', '请填写举报原因');
    }
    $report['addtime'] = $_timestamp;
    $report['uid'] = $user['uid'];
    lib_database :: insert('goods_report', array_keys($report), $report);
    message('0', '操作提示', '举报成功，我们会尽快处理');
}
function goodsover(){
    $num_iid = request('num_iid', '');
    $url = md5(request('url'));
    if(!empty($num_iid)){
        lib_database :: update("goods", array('issteal' => 1), 'num_iid in (\'' . $num_iid . '\')');
    }
    set_cache('config', $url, 1, 3600);
}
function brand($brand){
    global $_timestamp;
    if(!empty($brand['bid'])){
        $wherestr = 'bid=\'' . $brand['bid'] . '\'';
        unset($brand['bid']);
        lib_database :: update('brand', $brand, $wherestr);
    }else{
        $brand['addtime'] = $_timestamp;
        lib_database :: insert('brand', array_keys($brand), $brand);
    }
}
function getbrand($bid){
    return lib_database :: get_one('select * from ' . tname('brand') . ' where bid=\'' . $bid . '\'');
}
function brandlist($wherestr = array(), $order = '`sort` DESC,`start` DESC', $start = 0, $num = PAGE){
    global $_timestamp;
    $intkeys = array();
    $strkeys = array();
    $randkeys = array();
    $likekeys = array('brand' => 'keyword', 'remark' => 'keyword', 'nick' => 'keyword');
    $search = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
    !empty($search['wherearr']['brand'])?$where[] = $search['wherearr']['brand']:'';
    !empty($search['wherearr']['remark'])?$where[] = $search['wherearr']['remark']:'';
    !empty($search['wherearr']['nick'])?$where[] = $search['wherearr']['nick']:'';
    isset($where)?$wherestr[] = '(' . implode(' OR ', $where) . ')':'';
    unset($where);
    $countwhere = $wherestr = !empty($wherestr)?implode(' AND ', $wherestr):'1';
    !empty($order) && $wherestr .= ' ORDER BY ' . $order . '';
    if ($start > -1 && $num > 0){
        $wherestr .= " LIMIT {$start}, {$num}";
    }
    $data = array();
    $query = lib_database :: rquery('select * from ' . tname('brand') . ' where ' . $wherestr);
    while ($rt = lib_database :: fetch_one()){
        $data['bid_' . $rt['bid']] = $rt;
    }
    $output = array();
    $total = lib_database :: rquery('SELECT COUNT(*) AS rows from ' . tname('brand') . ' where ' . $countwhere);
    $total = lib_database :: fetch_one();
    $output['total'] = $total['rows'];
    $output['data'] = $data;
    foreach ($search['urls'] as $key => $value){
        $url = explode('=', $value);
        $urls[$url[0]] = $url[1];
    }
    $output['urls'] = isset($urls)?$urls:array();
    $output['url'] = implode('&', $search['urls']);
    $output['url'] = !empty($output['url'])?'&' . $output['url']:"";
    return $output;
}
function brandNid(){
    global $_nav;
    foreach ($_nav as $key => $value){
        if($value['mod'] == 'brand'){
            return $value['id'];
        }
    }
    return 0;
}
function deloverGoods(){
    global $_timestamp;
    $del = get_cache('goods', 'del');
    if($del < date('ymd')){
        lib_database :: delete('goods', 'end<' . $_timestamp . ' and status=1');
        set_cache('goods', 'del', date('ymd'));
    }
}
?>