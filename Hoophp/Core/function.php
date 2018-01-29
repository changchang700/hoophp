<?php
/**
 * 此文件为核心函数
 * @author Mumu
 * @date 2017年8月7日08:59:29
 */

/**
 * 配置操作
 * $value设置时设置值
 * $value不设置时读取值
 * @global array $config
 * @param type $key
 * @param type $value
 * @return type
 */
function config($key, $value = null) {
    global $config;
    //读取
    if ($value === null) {
        return $config[$key];
    }
    //设置
    $config[$key] = $value;
}
/**
 * 跳转URL
 * @param type $url
 */
function redirect($url) {
    Header("HTTP/1.1 301 Moved Permanently");
    Header("Location:{$url}");
    exit();
}
/**
 * JS强制跳转
 * @param type $url
 * @return string
 */
function redirect_js($url){
    $html  = " <script language='javascript' type='text/javascript'>";
    $html .= " window.location.href = '$url' ";
    $html .= " </script> ";
    echo $html;
    exit();
}

/**
 * session操作
 * 不指定$value时取session值
 * 否则设置为设置
 * 当$value为null时删除session值
 * @param string $key 操作key
 * @param mixed $value 操作值
 * @return mixed
 */
function session($key, $value = '') {
    if ($value === '') {
        return $_SESSION[$key];
    } else {
        $_SESSION[$key] = $value;
    }
}
/**
 * cookie操作
 * 不指定$value时取cookie值
 * 否则设置为设置
 * 当$value为null时删除cookie值
 * @param string $key 操作key
 * @param string $value 操作值
 * @param int $expirespan 有效时间长度（单位秒）
 * @param string $path 作用域路径
 * @param string $domain 作用域域名
 * @return mixed
 */
function cookie($key, $value = '', $expirespan = 0, $path = "/", $domain = null) {
    if ($value === '') {
        return $_COOKIE[$key];
    } else if ($value === null) {
        setcookie($key, '', time() - 3600, $path, $domain);
    } else {
        if ($expirespan != 0) {
            setcookie($key, $value, time() + $expirespan, $path, $domain);
        } else {
            setcookie($key, $value, $expirespan, $path, $domain);
        }
    }
}

/**
 * 创建一个ID
 * @return string
 */
function create_id() {
	$charid = strtoupper(md5(uniqid(mt_rand(), true)));
	$hyphen = chr(45);
	$uuid = substr($charid, 0, 8) . $hyphen
	 . substr($charid, 8, 4) . $hyphen
	 . substr($charid, 12, 4) . $hyphen
	 . substr($charid, 16, 4) . $hyphen
	 . substr($charid, 20, 12);
	return $uuid;
}

/**
 * 构造应用完整访问URL
 * @param string $path URL相对应用入口文件访问的路径和参数（分组应用应该包括分组标记）
 * @param mined $fields 请求的参数，可以字符串，也可以是键值对数组
 * @param mined $show_script 是否显示脚本
 * @return string 
 */
function get_url($path="", $fields = null,$show_script=true) {
    if($show_script){
        $url = get_protocal() . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] . "/" . $path;
    }else{
        $url = get_protocal() . "://" . $_SERVER["HTTP_HOST"] . "/" . $path;
    }
	if ($fields != null) {
		$url .= "?" . http_build_query($fields);
	}
	return $url;
}
/**
 * 获取访问的域名
 * @return string
 */
function get_host($url=''){
    return get_protocal().'://'.$_SERVER['SERVER_NAME'].'/' . $url;
}
/**
 * 取http协议
 * @return string
 */
function get_protocal() {
	$protocol = $_SERVER["HTTP_X_CLIENT_SCHEME"];
	if (!$protocol) {
		$protocol = $_SERVER["REQUEST_SCHEME"];
		if(!$protocol){
			$protocol = "http";
		}
	}
	return $protocol;
}

/**
 * 取指定目录下的所有目录（包括子目录）
 * @param string $dir
 * @return array
 */
function subdirs($dir) {
    $dir = rtrim(trim($dir), "/\\");
    $dir .= "/";
    $files = Array();
    $d = opendir($dir);
    while ($file = readdir($d)) {
        if ($file == '.' || $file == '..'){
            continue;
        }
        if (is_dir($dir . $file)) {
            $files[] = $dir . $file . '/';
            $files = array_merge($files,subdirs($dir . $file));
        }
    }
    closedir($d);
    return $files;
}
/**
 * 查看请求的连接是否是ajax请求
 * @return boolean
 */
function is_ajax(){
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest"){
        return true;
    }else{
        return false;
    }
}
/**
 * 设置HTTP状态码
 * @staticvar array $_status
 * @param type $code
 */
function send_http_status($code) {
    static $_status = array(
    200 => 'OK',
    301 => 'Moved Permanently',
    302 => 'Moved Temporarily ',
    400 => 'Bad Request',
    403 => 'Forbidden',
    404 => 'Not Found',
    500 => 'Internal Server Error',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    );
    if (isset($_status[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:' . $code . ' ' . $_status[$code]);
    }
}