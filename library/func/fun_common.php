<?php
if(!defined('PATH_ROOT')){
    exit('Access Denied');
}
function execute_mod($mod_name = 'index', $act_name = 'index'){
    if(V_MODE != 'vip' && in_array($mod_name, array('album', 'gift', 'seller'))){
        system :: show_check();
    }
    system :: check();
    try{
        $path_dir = PATH_APP . '/mod/' . $mod_name;
        $path_extend_dir = PATH_APP;
        if(is_readable($path_dir)){
            $path = $path_dir . '/' . $act_name . '.act.php';
            if (file_exists ($path)){
                return $path;
            }
            $path = $path_dir . '/common.inc.php';
            if (file_exists ($path)){
                return $path;
            }
        }elseif (is_readable($path_extend_dir)){
            $path = $path_extend_dir . '/mod/' . $act_name . '.act.php';
            if (file_exists ($path)){
                return $path;
            }
        }
        throw new Exception ("Contrl {$path} is not exists!");
    }
    catch (Exception $e){
        if (DEBUG_LEVEL === true){
            trigger_error("Load Controller false!");
        }else{
            header ("location:" . u('index', '404'));
            die ();
        }
    }
}
function tpl_mod($mod_name = 'index', $act_name = 'index'){
    try{
        $tpl_dir_name = PATH_TPL . '/' . $mod_name . '/' . $act_name . '.tpl.php';
        if (file_exists($tpl_dir_name)){
            return $tpl_dir_name;
        }else{
            $path = PATH_TPL . '/' . $mod_name . '/common.tpl';
            if (file_exists ($path)){
                return $path;
            }else{
                throw new Exception ("Contrl {$tpl_dir_name} is not exists!");
            }
        }
    }
    catch (Exception $e){
        if (DEBUG_LEVEL === true){
            trigger_error("Load Controller false!");
        }else{
            header ("location:" . u('index', '404'));
            die ();
        }
    }
}
function u($mod, $act, $arr = array()){
    global $_webset;
    $urlstr = $tag = '';
    $url = array();
    !empty($arr) && ksort($arr);
    if($_webset['rewrite_open'] == 1){
        $tag = '/';
        ($mod != 'index' && !empty($mod)) && $url[] = $mod;
        ($act != 'index' && !empty($act)) && $url[] = $act;
        if(!empty($arr)){
            foreach ($arr as $k => $v){
                if(!empty($v)){
                    $url[] = $k . '-' . $v;
                }
            }
        }
    }else{
        $tag = '&';
        ($mod != 'index' && !empty($mod)) && $url[] = 'mod=' . $mod;
        ($act != 'index' && !empty($act)) && $url[] = 'ac=' . $act;
        if(!empty($arr)){
            foreach ($arr as $k => $v){
                if(!empty($v)){
                    $url[] = $k . '=' . $v;
                }
            }
        }
    }
    $web_root = $_webset['site_url'] . '/';
    if(!empty($_webset[$mod . '_domain'])){
        if(preg_match('@^(?:(http|https)://)([^/]+)@i', $_webset[$mod . '_domain'])){
            $web_root = $_webset[$mod . '_domain'] . '/';
        }else{
            $web_root = "http://" . $_webset[$mod . '_domain'] . '/';
        }
    }
    $pre = '';
    if($_webset['rewrite_open'] < 1){
        $pre = '?';
    }
    $urlstr = (!empty($url) && count($url) >= 1)?$web_root . $pre . implode($tag, $url):$web_root;
    return $urlstr;
}
function test_write_able($d, $c = false){
    $tfile = '_write_able.txt';
    $d = preg_replace("/\/$/", '', $d);
    $fp = @fopen($d . '/' . $tfile, 'w');
    if(!$fp){
        if($c == false){
            @chmod($d, 0777);
            return false;
        }else{
            return test_write_able($d, true);
        }
    }else{
        fclose($fp);
        return @unlink($d . '/' . $tfile) ? true : false;
    }
}
function path_exists($path){
    $pathinfo = pathinfo ($path . '/tmp.txt');
    if (! empty ($pathinfo ['dirname'])){
        if (file_exists ($pathinfo ['dirname']) === false){
            if (mkdir ($pathinfo ['dirname'], 0777, true) === false){
                return false;
            }
        }
    }
    return $path;
}
function put_file($file, $content, $flag = 0){
    $pathinfo = pathinfo ($file);
    if (! empty ($pathinfo ['dirname'])){
        if (file_exists ($pathinfo ['dirname']) === false){
            if (@mkdir ($pathinfo ['dirname'], 0777, true) === false){
                return false;
            }
        }
    }
    if ($flag === FILE_APPEND){
        return @file_put_contents ($file, $content, FILE_APPEND);
    }else{
        return @file_put_contents ($file, $content, LOCK_EX);
    }
}
function get_cache($prefix, $key, $is_memcache = false){
    global $config;
    $key = md5 ($key);
    if ($is_memcache === true && ! empty ($config['memcache']) && $config['memcache'] ['is_mc_enable'] === true){
        $mc_path = empty ($config['memcache'] ['mc'] [substr ($key, 0, 1)]) ? $config['memcache'] ['mc'] ['default'] : $config['memcache'] ['mc'] [substr ($key, 0, 1)];
        $mc_path = parse_url ($mc_path);
        $key = ltrim ($mc_path ['path'], '/') . '_' . $prefix . '_' . $key;
        if (empty ($GLOBALS ['mc_' . $mc_path ['host']])){
            $GLOBALS ['mc_' . $mc_path ['host']] = new Memcache ();
            $GLOBALS ['mc_' . $mc_path ['host']] -> connect ($mc_path ['host'], $mc_path ['port']);
        }
        return $GLOBALS ['mc_' . $mc_path ['host']] -> get ($key);
    }
    $key = substr ($key, 0, 2) . '/' . substr ($key, 2, 2) . '/' . substr ($key, 4, 2) . '/' . $key;
    $result = @file_get_contents (PATH_DATA . "/cache/$prefix/$key");
    if ($result === false){
        return false;
    }
    $result = @unserialize ($result);
    if (!empty ($result ['timeout']) && $result ['timeout'] < time ()){
        return false;
    }
    return $result ['data'];
}
function set_cache($prefix, $key, $value, $timeout = 3600, $is_memcache = false){
    global $config;
    $key = md5 ($key);
    if (! empty ($config['memcache']) && $config['memcache'] ['is_mc_enable'] === true && $is_memcache === true){
        $mc_path = empty ($config['memcache'] ['mc'] [substr ($key, 0, 1)]) ? $config['memcache'] ['mc'] ['default'] : $config['memcache'] ['mc'] [substr ($key, 0, 1)];
        $mc_path = parse_url ($mc_path);
        $key = ltrim ($mc_path ['path'], '/') . '_' . $prefix . '_' . $key;
        if (empty ($GLOBALS ['mc_' . $mc_path ['host']])){
            $GLOBALS ['mc_' . $mc_path ['host']] = new Memcache ();
            $GLOBALS ['mc_' . $mc_path ['host']] -> connect ($mc_path ['host'], $mc_path ['port']);
        }
        $result = $GLOBALS ['mc_' . $mc_path ['host']] -> set ($key, $value, MEMCACHE_COMPRESSED, $timeout);
        return $result;
    }
    $key = substr ($key, 0, 2) . '/' . substr ($key, 2, 2) . '/' . substr ($key, 4, 2) . '/' . $key;
    $tmp ['data'] = $value;
    $tmp ['timeout'] = empty($timeout)?0:time () + (int) $timeout;
    return @put_file (PATH_DATA . "/cache/$prefix/$key", @serialize ($tmp));
}
function del_cache($prefix, $key, $is_memcache = false){
    global $config;
    $key = md5 ($key);
    if (! empty ($config['memcache']) && $config['memcache'] ['is_mc_enable'] === true && $is_memcache === true){
        $mc_path = empty ($config['memcache'] ['mc'] [substr ($key, 0, 1)]) ? $config['memcache'] ['mc'] ['default'] : $config['memcache'] ['mc'] [substr ($key, 0, 1)];
        $mc_path = parse_url ($mc_path);
        $key = ltrim ($mc_path ['path'], '/') . '_' . $prefix . '_' . $key;
        if (empty ($GLOBALS ['mc_' . $mc_path ['host']])){
            $GLOBALS ['mc_' . $mc_path ['host']] = new Memcache ();
            $GLOBALS ['mc_' . $mc_path ['host']] -> connect ($mc_path ['host'], $mc_path ['port']);
        }
        return $GLOBALS ['mc_' . $mc_path ['host']] -> delete ($key);
    }
    $key = substr ($key, 0, 2) . '/' . substr ($key, 2, 2) . '/' . substr ($key, 4, 2) . '/' . $key;
    return @unlink (PATH_DATA . "/cache/$prefix/$key");
}
function remove_invisible_characters($str, $url_encoded = TRUE){
    $non_displayables = array();
    if ($url_encoded){
        $non_displayables[] = '/%0[0-8bcef]/';
        $non_displayables[] = '/%1[0-9a-f]/';
    }
    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
    do{
        $str = preg_replace($non_displayables, '', $str, -1, $count);
    } while ($count);
    return $str;
}
function get_client_ip (){
    static $realip = NULL;
    if ($realip !== NULL){
        return $realip;
    }
    if (isset($_SERVER)){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR2'])){
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR2']);
            foreach ($arr as $ip){
                $ip = trim($ip);
                if ($ip != 'unknown'){
                    $realip = $ip;
                    break;
                }
            }
        }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr as $ip){
                $ip = trim($ip);
                if ($ip != 'unknown'){
                    $realip = $ip;
                    break;
                }
            }
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])){
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }else{
            if (isset($_SERVER['REMOTE_ADDR'])){
                $realip = $_SERVER['REMOTE_ADDR'];
            }else{
                $realip = '0.0.0.0';
            }
        }
    }else{
        if (getenv('HTTP_X_FORWARDED_FOR2')){
            $realip = getenv('HTTP_X_FORWARDED_FOR2');
        }elseif (getenv('HTTP_X_FORWARDED_FOR')){
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }elseif (getenv('HTTP_CLIENT_IP')){
            $realip = getenv('HTTP_CLIENT_IP');
        }else{
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = ! empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}
function get_cururl($_a = false){
    if(!empty($_SERVER["REQUEST_URI"])){
        $scriptName = $_SERVER["REQUEST_URI"];
        $nowurl = $scriptName;
    }else{
        $scriptName = $_SERVER["PHP_SELF"];
        $nowurl = empty($_SERVER["QUERY_STRING"]) ? $scriptName : $scriptName . "?" . $_SERVER["QUERY_STRING"];
    }
    $nowurl = 'http://' . $_SERVER['HTTP_HOST'] . $nowurl;
    return $nowurl;
}
function mkdir_recurse($pathname, $mode){
    $pathname = rtrim(preg_replace(array('/\\{1,}/', '/\/{2,}/'), '/', $pathname), '/');
    is_dir(dirname($pathname)) || mkdir_recurse(dirname($pathname), $mode);
    return is_dir($pathname) || @mkdir($pathname, $mode);
}
function rm_recurse($file){
    if (is_dir($file) && !is_link($file)){
        foreach(glob($file . '/*') as $sf){
            if (!rm_recurse($sf)){
                return false;
            }
        }
        return @rmdir($file);
    }else{
        return @unlink($file);
    }
}
function is_utf8($str){
    if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")){
        return true;
    }else{
        return false;
    }
}
function pagination ($config){
    $config['url'] = empty($config['url']) ? '' : $config['url'];
    $config['count_number'] = empty($config['count_number']) ? 0 : (int)$config['count_number'];
    $config['per_count'] = empty($config['per_count']) ? 10 : (int) $config['per_count'];
    $config['count_page'] = ceil($config['count_number'] / $config['per_count']);
    $config['page_name'] = empty($config['page_name']) ? 'start' : $config['page_name'];
    $config['current_page'] = max(1, ceil($config['start'] / $config['per_count']) + 1);
    if (empty($config) or $config['count_page'] < 2){
        return false;
    }
    $pages = '<div class="page">';
    $next_page = $config['start'] + $config['per_count'];
    $prev_page = $config['start'] - $config['per_count'];
    $last_page = ($config['count_page'] - 1) * $config['per_count'];
    $flag = 0;
    if($config['current_page'] > 1){
        $pages .= "<a href='{$config['url']}' class='nextprev'>&laquo;首页</a>\n";
        $pages .= "<a href='{$config['url']}&{$config['page_name']}={$prev_page}' class='nextprev'>&laquo;上一页</a>\n";
    }else{
        $pages .= "<span class='nextprev'>&laquo;首页</span>\n";
        $pages .= "<span class='nextprev'>&laquo;上一页</span>\n";
    }
    for ($i = $config['current_page'] - 6; $i <= $config['current_page'] - 1; $i ++){
        if ($i < 1){
            continue;
        }
        $_start = ($i - 1) * $config['per_count'];
        $pages .= "<a href='{$config['url']}&{$config['page_name']}=$_start'>$i</a>\n";
    }
    $pages .= "<span class='current'>" . $config['current_page'] . "</span>\n";
    if ($config['current_page'] < $config['count_page']){
        for ($i = $config['current_page'] + 1; $i <= $config['count_page']; $i ++){
            $_start = ($i - 1) * $config['per_count'];
            $pages .= "<a href='{$config['url']}&{$config['page_name']}=$_start'>$i</a>\n";
            $flag ++;
            if ($flag == 6){
                break;
            }
        }
    }
    if ($config['current_page'] != $config['count_page']){
        $pages .= "<a href='{$config['url']}&{$config['page_name']}={$next_page}' class='nextprev'>下一页&raquo;</a>\n";
        $pages .= "<a href='{$config['url']}&{$config['page_name']}={$last_page}'>末页&raquo;</a>\n";
    }else{
        $pages .= "<span class='nextprev'>下一页&raquo;</span>\n";
        $pages .= "<span class='nextprev'>末页&raquo;</span>\n";
    }
    if(!empty($config['input'])){
        $pages .= '<input type="text" onkeydown="javascript:if(event.keyCode==13){ var offset = ' . $config['per_count'] . '*(this.value-1);location=\'' . $config["url"] . '&' . $config["page_name"] . '=\'+offset;}" onkeyup="value=value.replace(/[^\d]/g,\'\')" />';
    }
    $pages .= "<span>共 {$config['count_page']} 页， {$config['count_number']} 条记录</span>\n";
    $pages .= '</div>';
    return $pages;
}
function utf8_substr($str, $slen, $startdd = 0){
    return mb_substr($str , $startdd , $slen , 'UTF-8');
}
function filter_keyword($str){
    $arr = array();
    preg_match_all("/./su", $str, $arr);
    $okstr = '';
    $fiter_arr = array('、', '。', '·', 'ˉ', 'ˇ', '¨', '〃', '々', '—', '～', '‖', '…', '‘', '’', '“', '”', '？', '：', '〔', '〕', '〈', '〉', '《', '》', '「', '」', '『', '』', '〖', '〗', '【', '】', '±', '×', '÷', '∶', '∧', '∨', '∑', '∏', '∪', '∩', '∈', '∷', '√', '⊥', '∥', '∠', '⌒', '⊙', '∫', '∮', '≡', '≌', '≈', '∽', '∝', '≠', '≮', '≯', '≤', '≥', '∞', '∵', '∴', '♂', '♀', '°', '′', '″', '℃', '＄', '¤', '￠', '￡', '‰', '§', '№', '☆', '★', '○', '●', '◎', '◇', '◆', '□', '■', '△', '▲', '※', '→', '←', '↑', '↓', '〓', '　', '！', '＂', '＃', '￥', '％', '＆', '＇', '（', '）', '＊', '＋', '，', '－', '．', '／', '；', '＜', '＝', '＞', '＠', '［', '＼', '］', '＾', '＿', '｀', '｛', '｜', '｝', '￣');
    foreach($arr[0] as $a){
        if(strlen($a) == 1 && !preg_match("/[0-9a-z@_:\.\+\-]/i", $a)){
            $okstr .= ' ';
        }else{
            $okstr .= in_array($a, $fiter_arr) ? ' ' : $a;
        }
    }
    $okstr = trim(preg_replace("/[ ]{1, }/", ' ', $okstr));
    return $okstr;
}
function send_email($to, $subject, $body){
    global $_webset;
    require PATH_LIBRARY . '/comm/lib_mail.php';
    try{
        if(empty($_webset['email_fromname'])){
            $_webset['email_fromname'] = $_webset['site_name'];
        }
        if($_webset['email_mod'] == 'mail'){
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=' . $_webset['email_charset'] . "\r\n";
            $headers .= 'To: <' . $to . '>' . "\r\n";
            $headers .= 'From: ' . $_webset['email_fromname'] . ' <' . $_webset['email_account'] . '>' . "\r\n";
            $result = mail($to, $subject, $body, $headers);
        }elseif($_webset['email_mod'] == 'smtp'){
            $smtp = new lib_mail($_webset['email_smtp'], $_webset['email_port'], true, $_webset['email_account'], $_webset['email_password']);
            $result = $smtp -> sendmail($to, $_webset['email_account'], $_webset['email_fromname'], $subject, $body, $_webset['email_charset']);
        }
        return $result;
    }
    catch(Exception $e){
        return false;
    }
}
function show_message ($title, $msg, $gourl, $limittime = 3000){
    show_msgbox(0, $title, $msg, $gourl, $limittime);
    exit();
}
function message($code, $title = '', $msg = '', $gourl = '', $data = '', $limittime = 3000){
    global $_isajax;
    if(!empty($_isajax)){
        if($gourl == 'javascript:;'){
            $gourl == '';
        }
        if($gourl == '-1'){
            $gourl = "javascript:history.go(-1);";
        }
        jsonp(json_encode(array('code' => $code, 'title' => $title, 'msg' => $msg, 'gourl' => $gourl, 'data' => $data, 'limittime' => $limittime)));
        exit();
    }else{
        show_msgbox($code, $title, $msg, $gourl, $limittime);
        exit();
    }
}
function show_msgbox($code, $title, $msg, $gourl = '', $limittime = 5000){
    global $_isajax, $_webset, $user, $_nav;
    if($title == '') $title = '系统提示信息';
    $jumpmsg = $jstmp = '';
    if($gourl == 'javascript:;'){
        $gourl == '';
    }
    if($gourl == '-1'){
        $gourl = "javascript:history.go(-1);";
    }
    if($gourl != ''){
        $jumpmsg = "<a href='{$gourl}' class='gotoPage'>如果你的浏览器没反应，请点击这里...</a>";
        $jstmp = "setTimeout('JumpUrl()', {$limittime});";
    }
    require trim(PATH_TPL, '/') . '/public/msgbox.tpl';
    exit();
}
function stripsearchkey($string){
    $string = trim($string);
    $string = str_replace('*', '%', addcslashes($string, '%_'));
    $string = str_replace('_', '\_', $string);
    return $string;
}
function getwheres($intkeys, $strkeys, $randkeys, $likekeys, $pre = ''){
    $wherearr = array();
    $urls = array();
    foreach ($intkeys as $key => $var){
        $value = isset(lib_request :: $forms[$var])?stripsearchkey(lib_request :: $forms[$var]):'';
        if(strlen($value)){
            $wherearr[$key] = "{$pre}`{$key}`='" . intval($value) . "'";
            $urls[] = "$var=$value";
        }
    }
    foreach ($strkeys as $key => $var){
        $value = isset(lib_request :: $forms[$key])?stripsearchkey(lib_request :: $forms[$var]):'';
        if(strlen($value)){
            $wherearr[$key] = "{$pre}`{$key}`='$value'";
            $urls[] = "$var=" . rawurlencode($value);
        }
    }
    foreach ($randkeys as $key => $vars){
        $value1 = isset(lib_request :: $forms[$vars[1] . '1'])?$vars[0](lib_request :: $forms[$vars[1] . '1']):'';
        $value2 = isset(lib_request :: $forms[$vars[1] . '2'])?$vars[0](lib_request :: $forms[$vars[1] . '2']):'';
        $value3 = isset(lib_request :: $forms[$vars[1] . '3'])?$vars[0](lib_request :: $forms[$vars[1] . '3']):'';
        $value4 = isset(lib_request :: $forms[$vars[1] . '4'])?$vars[0](lib_request :: $forms[$vars[1] . '4']):'';
        if($value1){
            $wherearr[$key] = "{$pre}`{$key}`>='$value1'";
            $urls[] = "{$vars[1]}1=" . rawurlencode(lib_request :: $forms[$vars[1] . '1']);
        }
        if($value2){
            $wherearr[$key] = "{$pre}`{$key}`<='$value2'";
            $urls[] = "{$vars[1]}2=" . rawurlencode(lib_request :: $forms[$vars[1] . '2']);
        }
        if($value3){
            $wherearr[$key] = "{$pre}`{$key}`>'$value3'";
            $urls[] = "{$vars[1]}3=" . rawurlencode(lib_request :: $forms[$vars[1] . '3']);
        }
        if($value4){
            $wherearr[$key] = "{$pre}`{$key}`<='$value4'";
            $urls[] = "{$vars[1]}4=" . rawurlencode(lib_request :: $forms[$vars[1] . '4']);
        }
    }
    foreach ($likekeys as $key => $var){
        $value = isset(lib_request :: $forms[$var])?stripsearchkey(lib_request :: $forms[$var]):'';
        if(strlen($value) > 1){
            $wherearr[$key] = "{$pre}`{$key}` LIKE BINARY '%$value%'";
            $urls[] = "$var=" . rawurlencode($value);
        }
    }
    return array('wherearr' => $wherearr, 'urls' => $urls);
}
function get_page_number_list($total, $start, $page_rows = PAGE, $default_pages = 7){
    if ($total < 1 || $start < 0 || $page_rows < 1) return false;
    if(ceil($total / $page_rows) < 2){
        return false;
    }
    $least = ($default_pages - 5) / 2;
    if ($start < $page_rows) $start = 0;
    if ($start >= $total) $start = $total - 1;
    $page_num = ceil($total / $page_rows);
    $current_page = ceil($start / $page_rows);
    if ($start % $page_rows == 0) $current_page++;
    if ($current_page < 1) $current_page = 1;
    $output = array();
    if ($page_num <= $default_pages){
        $prev = ($current_page-2) * $page_rows;
        if ($prev < 0) $prev = 0;
        if ($start > 0) $output['prev'] = $prev;
        for ($i = 0; $i < $page_num; ++$i){
            $tmp = $i * $page_rows;
            $t = $i + 1;
            if ($t == $current_page) $output[$t] = -1;
            else $output[$t] = $tmp;
        }
        $next = $current_page * $page_rows;
        if ($next < $total) $output['next'] = $next;
    }else{
        if ($current_page - $least - 1 > 1 || $current_page + $least + 1 < $page_num){
            $prev = ($current_page - 2) * $page_rows;
            if ($prev < 0) $prev = 0;
            if ($start > 0) $output['prev'] = $prev;
            for ($i = 0; $i < $page_num; ++$i){
                $tmp = $i * $page_rows;
                $t = $i + 1;
                if ($t < $current_page - $least && $page_num - $default_pages + 2 > $t && $t != 1){
                    $output['omitf'] = true;
                    continue;
                }
                if ($t > $current_page + $least && $t > $default_pages - 1 && $t != $page_num){
                    $output['omita'] = true;
                    continue;
                }
                if ($t == $current_page) $output[$t] = -1;
                else $output[$t] = $tmp;
            }
            $next = $current_page * $page_rows;
            if ($next < $total) $output['next'] = $next;
        }else{
            $prev = ($current_page-2) * $page_rows;
            if ($prev < 0) $prev = 0;
            if ($start > 0) $output['prev'] = $prev;
            for ($i = 0; $i < $page_num; ++$i){
                $tmp = $i * $page_rows;
                $t = $i + 1;
                if ($t == $current_page) $output[$t] = -1;
                else $output[$t] = $tmp;
            }
            $next = $current_page * $page_rows;
            if ($next < $total) $output['next'] = $next;
        }
    }
    return $output;
}
function jsonp($json){
    $callback = trim(request("callback"));
    echo $callback . '(' . $json . ')';
    exit();
}
function array_merge_multi(){
    $args = func_get_args();
    if (!isset($args[0]) && !array_key_exists(0, $args)){
        return array();
    }
    $arr = array();
    foreach ($args as $key => $value){
        if (is_array($value)){
            foreach ($value as $k => $v){
                if (is_array($v)){
                    if (!isset($arr[$k]) && !array_key_exists($k, $arr)){
                        $arr[$k] = array();
                    }
                    $arr[$k] = array_merge_multi($arr[$k], $v);
                }else{
                    $arr[$k] = $v;
                }
            }
        }
    }
    return $arr;
}
function curl($url, $postFields = null){
    if(!function_exists('curl_init')){
        exit('php.ini php_curl must is Allow! ');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if (is_array($postFields) && 0 < count($postFields)){
        $postBodyString = "";
        foreach ($postFields as $k => $v){
            $postBodyString .= "$k=" . urlencode($v) . "&";
        }
        unset($k, $v);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
    }
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,sdch');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36 SE 2.X MetaSr 1.0');
    $reponse = curl_exec($ch);
    if ($reponse === false){
        exit(curl_error($ch));
    }else{
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 !== $httpStatusCode){
            exit($reponse . '&nbsp;code:' . $httpStatusCode);
        }
    }
    curl_close($ch);
    return $reponse;
}
function _put_cookie($key, $value, $keeptime = 0, $encode = true){
    global $config;
    $keeptime = $keeptime == 0 ? null : time() + $keeptime;
    setcookie($config['cookie_pre'] . $key, $value, $keeptime, COOKIE_PATH);
    if($encode){
        setcookie($config['cookie_pre'] . $key . $config['cookie_pwd'], substr(md5($config['cookie_pwd'] . $value), 0, 24), $keeptime, COOKIE_PATH);
    }
}
function _drop_cookie($key, $encode = true){
    global $config;
    setcookie($config['cookie_pre'] . $key, '', time()-360000, '/', COOKIE_DOMAIN);
    if($encode){
        setcookie($config['cookie_pre'] . $key . $config['cookie_pwd'], '', time()-360000, COOKIE_PATH);
    }
}
function get_cookie($key, $encode = true){
    global $config;
    $key = $config['cookie_pre'] . $key;
    if(!isset($_COOKIE[$key])){
        return '';
    }else{
        if($encode){
            $epwd = substr(md5($config['cookie_pwd'] . $_COOKIE[$key]), 0, 24);
            if(!isset($_COOKIE[$key . $config['cookie_pwd']])) return '';
            else return ($_COOKIE[$key . $config['cookie_pwd']] != $epwd) ? '' : $_COOKIE[$key];
        }else{
            return ($_COOKIE[$key] != $epwd) ? '' : $_COOKIE[$key];
        }
    }
}
function gather(){
    global $_webset, $_timestamp;
    $goods = request('goods', array());
    $inserthtml = $tag = '';
    foreach ($goods as $key => $value){
        $value['discount'] = $value['discount'];
        $value['issite'] = empty($value['issite'])?-1:$value['issite'];
        if(!empty($value['title']) && !empty($value['channel']) && !empty($value['cat']) && !empty($value['num_iid'])){
            if(empty($value['addtime'])){
                $value['addtime'] = $_timestamp;
            }
            if(!empty($_webset['base_showday']) && $_webset['base_showday'] > 0){
                $value['end'] = $_webset['base_showday'] * 3600 * 24 + $value['start'];
            }
            $inserthtml .= $tag . '(\'' . strip_tags($value['title']) . '\',
								\'' . $value['channel'] . '\',
								\'' . $value['cat'] . '\',
								\'' . $value['price'] . '\',
								\'' . $value['promotion_price'] . '\',
								\'' . $value['discount'] . '\',								
								\'' . $value['volume'] . '\',
								\'' . $value['nick'] . '\',
								\'' . $value['site'] . '\',
								\'' . $value['num_iid'] . '\',
								\'' . $value['pic'] . '\',
								\'' . $value['taopic'] . '\',
								\'' . $value['taopicl'] . '\',
								\'' . $value['ispost'] . '\',
								\'' . $value['isvip'] . '\',
								\'' . $value['isrec'] . '\',
								\'' . $value['issite'] . '\',
								\'' . $value['addtime'] . '\',
								\'' . $value['start'] . '\',
								\'' . $value['end'] . '\',
								\'' . $value['status'] . '\',
								\'' . $value['seller_id'] . '\',
								\'' . $value['remark'] . '\'
								)';
            $tag = ',';
        }
    }
    if(!empty($inserthtml)){
        $inserthtml = 'replace into ' . tname('goods') . '(`title`,
										`channel`, 
										`cat`, 
										`price`, 
										`promotion_price`, 
										`discount`, 
										`volume`,
										`nick`, 
										`site`, 
										`num_iid`, 
										`pic`, 
										`taopic`,
										`taopicl`, 
										`ispost`,
										`isvip`,
										`isrec`,
										`issite`,
										`addtime`, 
										`start`, 
										`end`,
										`status`,
										`seller_id`,
										`remark`
										) VALUES ' . $inserthtml . ';';
        lib_database :: wquery($inserthtml);
    }
    if(count($goods) == 1){
        $good = array_shift($goods);
        message(0, '采集提示', '采集到数据' . $good['title']);
    }else{
        message(0, '采集提示', '采集成功');
    }
}
function brandgather(){
    global $_webset, $_timestamp;
    $data = request('data', array());
    $type = request('type', 'brand');
    $inserthtml = $tag = '';
    if($type == 'brand'){
        if(empty($data['addtime'])){
            $data['addtime'] = $_timestamp;
        }
        $logo_url = $data['logo'];
        $pic_url = $data['pic'];
        if(!empty($_webset['base_showday']) && $_webset['base_showday'] > 0){
            $data['end'] = $_webset['base_showday'] * 3600 * 24 + $data['start'];
        }
        $brand = lib_database :: get_one('select * from ' . tname('brand') . ' where sbid=' . $data['bid']);
        $pic_url = $data['pic'];
        lib_database :: wquery('replace into ' . tname('brand') . '(`bid`,`brand`,`preferential`,`url`,`nick`,`remark`,`logo`,`pic`,`start`,`end`,`addtime`,`sort`,`sbid`) VALUES(\'' . intval($brand['bid']) . '\',\'' . $data['brand'] . '\',\'' . $data['preferential'] . '\',\'' . $data['url'] . '\',\'' . $data['nick'] . '\',\'' . $data['remark'] . '\',\'' . $logo_url . '\',\'' . $pic_url . '\',\'' . $data['start'] . '\',\'' . $data['end'] . '\',\'' . $data['addtime'] . '\',\'' . intval($data['sort']) . '\',\'' . $data['bid'] . '\')');
        message(0, '采集提示', '采集到品牌' . $data['brand']);
    }elseif ($type == 'goods'){
        $data['issite'] = empty($data['issite'])?-1:$data['issite'];
        if(!empty($data['title']) && !empty($data['channel']) && !empty($data['num_iid'])){
            if(empty($data['addtime'])){
                $data['addtime'] = $_timestamp;
            }
            $data['pic'] = $data['pic1'];
            if(!empty($_webset['base_showday']) && $_webset['base_showday'] > 0){
                $data['end'] = $_webset['base_showday'] * 3600 * 24 + $data['start'];
            }
            $brand = lib_database :: get_one('select bid from ' . tname('brand') . ' where sbid=' . $data['bid']);
            if(!empty($brand['bid'])){
                $inserthtml .= '(\'' . strip_tags($data['title']) . '\',
								\'' . $data['channel'] . '\',
								\'' . $brand['bid'] . '\',
								\'' . $data['price'] . '\',
								\'' . $data['promotion_price'] . '\',
								\'' . $data['discount'] . '\',								
								\'' . $data['volume'] . '\',
								\'' . $data['nick'] . '\',
								\'' . $data['site'] . '\',
								\'' . $data['num_iid'] . '\',
								\'' . $data['pic'] . '\',
								\'' . $data['pic_url'] . '\',
								\'' . $data['ispost'] . '\',
								\'' . $data['isvip'] . '\',
								\'' . $data['issite'] . '\',
								\'' . $data['addtime'] . '\',
								\'' . $data['start'] . '\',
								\'' . $data['end'] . '\',
								\'1\',
								\'' . $data['seller_id'] . '\',
								\'' . $data['remark'] . '\'
								)';
                if(!empty($inserthtml)){
                    $inserthtml = 'replace into ' . tname('goods') . '(`title`,
												`channel`, 
												`cat`, 
												`price`, 
												`promotion_price`, 
												`discount`, 
												`volume`,
												`nick`, 
												`site`, 
												`num_iid`, 
												`pic`,
												`taopic`,  
												`ispost`,										
												`isvip`,
												`issite`,
												`addtime`, 
												`start`, 
												`end`,
												`status`,
												`seller_id`,
												`remark`
												) VALUES ' . $inserthtml . ';';
                    lib_database :: wquery($inserthtml);
                }
            }
        }
        message(0, '采集提示', '采集到数据' . $data['title']);
    }
    message(0, '采集提示', '为采集到数据');
}
function code62($x){
    $show = '';
    while($x > 0){
        $s = $x % 62;
        if ($s > 35){
            $s = chr($s + 61);
        }elseif ($s > 9 && $s <= 35){
            $s = chr($s + 55);
        }
        $show .= $s;
        $x = floor($x / 62);
    }
    return $show;
}
function shortstr($str){
    $str = crc32($str);
    $result = sprintf("%u", $str);
    return code62($result);
}
function creat_code(){
    $year_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $str = $year_code[intval(date('Y'))-2014] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return shortstr($str);
}
function ajaxfile(){
    foreach (lib_request :: $files as $filename => $value){
        $picname = lib_request :: $files[$filename]['name'];
        $picsize = lib_request :: $files[$filename]['size'];
        if ($picname != ""){
            if ($picsize > 1024000){
                echo json_encode(array('code' => '-1', 'title' => '图片上传', 'msg' => '图片大小不能超过1M'));
                exit();
            }
            $type = get_extension($picname);
            if ($type != "gif" && $type != "jpg" && $type != "jpeg" && $type != "png"){
                echo json_encode(array('code' => '-1', 'title' => '图片上传', 'msg' => '图片格式不对！'));
                exit();
            }
            $rand = rand(100, 999);
            $pics = date("YmdHis") . $rand . '.' . $type;
            $dir = date('/Y/m/d/H/');
            $pic_path = PATH_UPLOAD . $dir;
            $pic_url = $pic_path . $pics;
            if(!file_exists(PATH_ROOT . $pic_path)){
                creatFolder(PATH_ROOT . $pic_path);
            }
            $pic_path = PATH_ROOT . $pic_url;
            move_uploaded_file(lib_request :: $files[$filename]['tmp_name'], $pic_path);
            if(request('type') == 'goods'){
                makeThumbnail($pic_path, $pic_path . '_290x290.jpg', 290, 190);
            }
        }
        $arr[$filename] = array('name' => $picname, 'pic' => $pic_url, 'size' => $picsize);
    }
    echo json_encode($arr);
    exit();
}
function creatFolder($Folder){
    $webbase = $Folder;
    if (strrchr($webbase , "/") != "/"){
        $webbase .= "/";
    }
    if (! file_exists($webbase)){
        if (! mkdir($webbase , 0777 , true)){
            return false;
        }
    }
    return $webbase;
}
function makeThumbnail($srcImgPath, $targetImgPath, $targetW, $targetH){
    $imgSize = GetImageSize($srcImgPath);
    $imgType = $imgSize[2];
    switch ($imgType){
    case 1: $srcImg = @ImageCreateFromGIF($srcImgPath);
        break;
    case 2: $srcImg = @ImageCreateFromJpeg($srcImgPath);
        break;
    case 3: $srcImg = @ImageCreateFromPNG($srcImgPath);
        break;
    }
    $srcW = ImageSX($srcImg);
    $srcH = ImageSY($srcImg);
    if($srcW > $targetW || $srcH > $targetH){
    $targetX = 0;
    $targetY = 0;
    if ($srcW > $srcH){
    $finaW = $targetW;
    $finalH = round($srcH * $finaW / $srcW);
    $targetY = floor(($targetH - $finalH) / 2);
}else{
    $finalH = $targetH;
    $finaW = round($srcW * $finalH / $srcH);
    $targetX = floor(($targetW - $finaW) / 2);
}
if(function_exists("ImageCreateTrueColor")){
    $targetImg = ImageCreateTrueColor($targetW, $targetH);
}else{
    $targetImg = ImageCreate($targetW, $targetH);
}
$targetX = ($targetX < 0)?0:$targetX;
$targetY = ($targetX < 0)?0:$targetY;
$targetX = ($targetX > ($targetW / 2))?floor($targetW / 2):$targetX;
$targetY = ($targetY > ($targetH / 2))?floor($targetH / 2):$targetY;
$white = ImageColorAllocate($targetImg, 255, 255, 255);
ImageFilledRectangle($targetImg, 0, 0, $targetW, $targetH, $white);
if(function_exists("ImageCopyResampled")){
    ImageCopyResampled($targetImg, $srcImg, $targetX, $targetY, 0, 0, $finaW, $finalH, $srcW, $srcH);
}else{
    ImageCopyResized($targetImg, $srcImg, $targetX, $targetY, 0, 0, $finaW, $finalH, $srcW, $srcH);
}
switch ($imgType){
case 1: ImageGIF($targetImg, $targetImgPath);
    break;
case 2: ImageJpeg($targetImg, $targetImgPath, 100);
    break;
case 3: ImagePNG($targetImg, $targetImgPath);
    break;
}
ImageDestroy($srcImg);
ImageDestroy($targetImg);
}else{
copy($srcImgPath, $targetImgPath);
ImageDestroy($srcImg);
}
}
function get_extension($file){
$info = pathinfo($file);
return strtolower($info['extension']);
}
function tname($tablename){
global $config;
return $config['db_pre'] . $tablename;
}
function filterwords($string){
global $_webset;
if($_webset['base_sensitive'] != 0){
@$filterwords = require(FILTER_WORD);
if(!empty($filterwords)){
    if($_webset['base_sensitive'] == 1){
        if(preg_match("/$filterwords/i", $string)){
            return false;
        }
        return $string;
    }elseif($_webset['base_sensitive'] == -1){
        return preg_replace("/$filterwords/i", empty($_webset['base_sensitive_tag'])?'*':$_webset['base_sensitive_tag'], $string);
    }
}
}
return $string;
}
/////////////////////////////////////////////////////////////////////////
////////////                   //////////////////////////////////////////
///////////   商品详细页面链接   ////////////////////////////////////////
///////////                   ///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
function get_taoke($iid, $rf = ''){
global $_timestamp, $_webset;
preg_match('/pid:\s+\"(.*?)\"/', $_webset['taoke_dianjin'], $pid);
$pid = $pid[1];
$pgid = md5($iid);
if(empty($rf)){
$rf = urlencode(u('index', 'jump', array('iid' => $iid)));
if(!preg_match('/^http/', $rf)){
    $rf = urlencode($_webset['site_url'] . '/') . $rf;
}
}else{
$rf = urlencode($rf);
}
$url = 'http://localhost/tiancheng/tiancheng?rf=' . $rf . '&pid=' . $pid . '&pgid=' . $pgid . '&cbh=261&cbw=1436&re=1440x900&cah=870&caw=1440&ccd=32&ctz=8&chl=2&cja=1&cpl=0&cmm=0&cf=10.0&cb=jsonp_callback_004967557514815568';
$a = curl($url);
preg_match('/jsonp_callback_\d+\(\{\"code\":\"(.*?)\"\}\)/', $a, $b);
if($b[1] != ''){
preg_match_all('/[\w\d]/', $b[1], $b);
$b[1] = implode('', $b[0]);
$url = 'http://localhost/tiancheng/tiancheng/display?cb=jsonp_callback_03655084007659234&pid=' . $pid . '&wt=0&ti=7&tl=628x100&rd=1&ct=itemid%3D' . $iid . '&st=2&rf=' . $rf . '&et=' . $b[1] . '&pgid=' . $pgid . '&v=2.0';
$a = curl($url);
preg_match('/jsonp_callback_\d+\((.*?)\)/', $a, $b);
$json = $b[1];
}
$a = json_decode($json, 1);
if(is_array($a)){
if($a['code'] != 200){
    return false;
}else{
    $goods['nick'] = $a['data']['items'][0]['ds_nick'];
    $goods['seller_id'] = $a['data']['items'][0]['ds_user_id'];
    $goods['img'] = $a['data']['items'][0]['ds_img']['src'];
    $goods['site'] = !empty($a['data']['items'][0]['ds_istmall'])?'tmall':'taobao';
    $goods['price'] = $a['data']['items'][0]['ds_reserve_price'];
    $goods['promotion_price'] = $a['data']['items'][0]['ds_discount_price'];
    if($goods['price'] <= $goods['promotion_price']){
        $goods['promotion_price'] = 0;
    }
    $goods['click_url'] = $a['data']['items'][0]['ds_item_click'];
    $goods['shop_click_url'] = $a['data']['items'][0]['ds_shop_click'];
    $goods['volume'] = $a['data']['items'][0]['ds_sell'];
    return $goods;
}
}else{
return false;
}
}
function get_taobao_comment($type, $iid, $sid = 0){
$contentarr = get_cache('goods', 'taobao_comment_' . $iid);
if(empty($contentarr)){
$contentarr = array('total' => 0, 'list' => array());
if($type == 'tmall'){
    $url = 'http://rate.tmall.com/list_detail_rate.htm?itemId=' . $iid . '&sellerId=' . $sid . '&currentPage=1&content=1&callback=';
}elseif ($type == 'taobao'){
    if(empty($sid)){
        $sid = get_seller_id($iid);
    }
    if(empty($sid)){
        return $contentarr;
    }
    $url = 'http://rate.taobao.com/feedRateList.htm?callback=&userNumId=' . $sid . '&auctionNumId=' . $iid . '&currentPageNum=1';
    $url2 = 'http://orate.alicdn.com/detailCommon.htm?callback=&userNumId=' . $sid . '&auctionNumId=' . $iid;
    $totalcontent = curl($url2);
    $totalcontent = iconv('gbk', 'utf-8', $totalcontent);
    preg_match('/\((.*?)\)/', $totalcontent, $totaldata);
    $totalcontent = $totaldata[1];
    $totalcontent = json_decode($totalcontent, true);
    if(isset($totalcontent['data']['count']['total'])){
        $contentarr['total'] = $totalcontent['data']['count']['total'];
    }
    if(empty($contentarr['total'])){
        return $contentarr;
    }
}
$content = curl($url);
if(!function_exists('iconv')){
    exit('iconv is not found');
}
$content = iconv('gbk', 'utf-8', $content);
if($type == 'taobao'){
    preg_match('/\((.*?)\)/', $content, $data);
    $content = $data[1];
}else{
    $content = '{' . $content . '}';
}
$content = json_decode($content, true);
if($type == 'tmall' && isset($content['rateDetail']['rateList']) && !empty($content['rateDetail']['rateList']) && is_array($content['rateDetail']['rateList'])){
    $contentarr['total'] = $content['rateDetail']['rateCount']['total'];
    foreach ($content['rateDetail']['rateList'] as $key => $value){
        preg_match('/b_(.*?_\d+)\.gif/', $value['displayRatePic'], $RateSum);
        $contentarr['list'][] = array('UserNick' => $value['displayUserNick'], 'Content' => $value['rateContent'], 'Date' => $value['rateDate'], 'RateSum' => $RateSum[1], 'tamllSweetLevel' => $value['tamllSweetLevel'],);
    }
}elseif ($type == 'taobao' && isset($content['comments']) && !empty($content['comments']) && is_array($content['comments'])){
    foreach ($content['comments'] as $key => $value){
        preg_match('/b_(.*?_\d+)\.gif/', $value['user']['displayRatePic'], $RateSum);
        $contentarr['list'][] = array('UserNick' => $value['user']['nick'], 'Content' => $value['content'], 'Date' => $value['date'], 'RateSum' => $RateSum[1], 'tamllSweetLevel' => 0,);
    }
}
set_cache('goods', 'taobao_comment_' . $iid, $contentarr);
}
return $contentarr;
}
function get_ai_tao($url){
$url = explode('&', $url);
foreach ($url as $val){
$val = explode("=", $val);
if($val[0] == "f"){
    $url = $val[1];
    continue;
}
}
$url = urldecode($url);
if(0 < strpos($url, 're.taobao.com')){
return $url;
}
$content = curl($url);
$firstIndex = strpos($content, 'class="big-img" href="');
$theStr = substr($content, $firstIndex + strlen('class="big-img" href="'));
$firstNext = strpos($theStr, '"');
if ($firstIndex === FALSE || $firstNext === FALSE){
return $url;
}
$str1 = trim(substr($theStr, 0, $firstNext));
return $str1;
}
function go_ai_taobao($url, $time = 0){
$s = "<script language=\"javascript\">var iurl=\"" . $url . "\";document.write(\"<meta http-equiv=\\\"refresh\\\" content=\\\"0;url=\"+iurl+\"\\\" />\");</script>";
if (strpos($_SERVER['HTTP_USER_AGENT'], "AppleWebKit") && !strpos($_SERVER['HTTP_USER_AGENT'], 'LBBROWSER')){
$s = "<script language=\"javascript\">var iurl=\"data:text/html;base64," . base64_encode($s) . "\";document.write(\"<meta http-equiv=\\\"refresh\\\" content=\\\"" . $time . ";url=\"+iurl+\"\\\" />\");</script>";
}else{
$s = str_replace("\"0;", "\"" . $time . ";", $s);
}
echo $s;
exit();
}
function get_seller_id($iid){
$goods = get_taoke($iid);
if(!empty($goods['seller_id'])){
lib_database :: wquery('UPDATE `' . tname('goods') . '` set `seller_id`=' . $goods['seller_id'] . ' where num_iid=\'' . $iid . '\'');
return $goods['seller_id'];
}else{
return 0;
}
}
function page_url($url, $start, $startname = 'start'){
global $_webset;
if($_webset['rewrite_open'] != 1){
if(strpos($url, '&') === false && strpos($url, '?') === false){
    return $url . '?' . $startname . '=' . $start;
}else{
    return $url . '&' . $startname . '=' . $start;
}
}else{
return rtrim($url, '/') . '/' . $startname . '-' . $start;
}
}
function get_img($url, $w = ''){
global $_webset;
!empty($w)?$w = '_' . $w . 'x' . $w . '.jpg':'';
preg_match("/^(http:\/\/).*?/", $url, $server);
if(!empty($server[1])){
return $url . $w;
}else{
return $_webset['site_url'] . '/' . $url;
}
}
function submitcheck($var, $allowget = 0){
if(!request($var)){
return false;
}else{
$formhash = request('formhash', '');
if($allowget || ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($formhash) && $formhash == formhash() && empty($_SERVER['HTTP_X_FLASH_VERSION']) && (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))){
    return true;
}else{
    return false;
}
}
}
function formhash($specialadd = ''){
global $_timestamp, $config, $access;
return substr(md5(substr($_timestamp, 0, -7) . $config['authkey'] . $specialadd), 8, 8);
}
function echo_validate_image($config = array()){
@session_start();
$font_size = isset($config['font_size']) ? $config['font_size'] : 14;
$img_height = isset($config['img_height']) ? $config['img_height'] : 24;
$img_width = isset($config['img_width']) ? $config['img_width'] : 68;
$font_file = isset($config['font_file']) ? $config['font_file'] : PATH_DATA . '/font/ggbi.ttf';
$use_boder = isset($config['use_boder']) ? $config['use_boder'] : true;
$filter_type = isset($config['filter_type']) ? $config['filter_type'] : 0;
$im = imagecreate($img_width, $img_height);
ImageColorAllocate($im, 255, 255, 255);
$fontColor[] = ImageColorAllocate($im, 0x15, 0x15, 0x15);
$fontColor[] = ImageColorAllocate($im, 0x95, 0x1e, 0x04);
$fontColor[] = ImageColorAllocate($im, 0x93, 0x14, 0xa9);
$fontColor[] = ImageColorAllocate($im, 0x12, 0x81, 0x0a);
$fontColor[] = ImageColorAllocate($im, 0x06, 0x3a, 0xd5);
$rndstring = '';
for($i = 0; $i < 4; $i++){
$c = chr(mt_rand(65, 90));
if($c == 'I') $c = 'P';
if($c == 'O') $c = 'N';
$rndstring .= $c;
}
$_SESSION['ckstr'] = strtolower($rndstring);
$rndcodelen = strlen($rndstring);
$lineColor1 = ImageColorAllocate($im, 0xda, 0xd9, 0xd1);
for($j = 3; $j <= $img_height-3; $j = $j + 3){
imageline($im, 2, $j, $img_width - 2, $j, $lineColor1);
}
$lineColor2 = ImageColorAllocate($im, 0xda, 0xd9, 0xd1);
for($j = 2;$j < 100;$j = $j + 6){
imageline($im, $j, 0, $j + 8, $img_height, $lineColor2);
}
if($use_boder && $filter_type == 0){
$bordercolor = ImageColorAllocate($im, 0x9d, 0x9e, 0x96);
imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $bordercolor);
}
$lastc = '';
for($i = 0;$i < $rndcodelen;$i++){
$bc = mt_rand(0, 1);
$rndstring[$i] = strtoupper($rndstring[$i]);
$c_fontColor = $fontColor[mt_rand(0, 4)];
$y_pos = $i == 0 ? 4 : $i * ($font_size + ($img_width / $rndcodelen - $font_size));
$c = mt_rand(0, 15);
@imagettftext($im, $font_size, $c, $y_pos, ($img_height - $font_size / 2), $c_fontColor, $font_file, $rndstring[$i]);
$lastc = $rndstring[$i];
}
switch($filter_type){
case 1: imagefilter ($im, IMG_FILTER_NEGATE);
break;
case 2: imagefilter ($im, IMG_FILTER_EMBOSS);
break;
case 3: imagefilter ($im, IMG_FILTER_EDGEDETECT);
break;
default: break;
}
header("Pragma:no-cache\r\n");
header("Cache-Control:no-cache\r\n");
header("Expires:0\r\n");
if(function_exists("imagejpeg")){
header("content-type:image/jpeg\r\n");
imagejpeg($im);
}else{
header("content-type:image/png\r\n");
imagepng($im);
}
ImageDestroy($im);
exit();
}
function tpl_extend($tplname = 'index'){
try{
$tpl_dir_name = PATH_APP . '/template/' . $tplname;
if (file_exists($tpl_dir_name)){
return $tpl_dir_name;
}else{
throw new Exception ("Contrl {$tpl_dir_name} is not exists!");
}
}
catch (Exception $e){
if (DEBUG_LEVEL === true){
trigger_error("Load Controller false!");
}else{
header ("location:" . u('index', '404'));
die ();
}
}
}
function extend_message($code, $title = '', $msg = '', $gourl = '', $data = '', $limittime = 3000){
global $_isajax;
if(!empty($_isajax)){
if($gourl == 'javascript:;'){
$gourl == '';
}
if($gourl == '-1'){
$gourl = "javascript:history.go(-1);";
}
jsonp(json_encode(array('code' => $code, 'title' => $title, 'msg' => $msg, 'gourl' => $gourl, 'data' => $data, 'limittime' => $limittime)));
exit();
}else{
extend_show($code, $title, $msg, $gourl, $limittime);
exit();
}
}
function extend_show($code, $title, $msg, $gourl = '', $limittime = 5000){
global $_isajax, $_webset, $user, $_nav;
if($title == '') $title = '系统提示信息';
$jumpmsg = $jstmp = '';
if($gourl == 'javascript:;'){
$gourl == '';
}
if($gourl == '-1'){
$gourl = "javascript:history.go(-1);";
}
if($gourl != ''){
$jumpmsg = "<a href='{$gourl}' class='gotoPage'>如果你的浏览器没反应，请点击这里...</a>";
$jstmp = "setTimeout('JumpUrl()', {$limittime});";
}
require tpl_extend('pub/msgbox.tpl');
exit();
}
function tpl_plugin($tplname = 'index'){
try{
$tpl_dir_name = PATH_APP . '/template/' . $tplname;
if (file_exists($tpl_dir_name)){
return $tpl_dir_name;
}else{
throw new Exception ("Contrl {$tpl_dir_name} is not exists!");
}
}
catch (Exception $e){
if (DEBUG_LEVEL === true){
trigger_error("Load Controller false!");
}else{
header ("location:" . u('index', '404'));
die ();
}
}
}
?>