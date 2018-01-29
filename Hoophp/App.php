<?php
require_once __DIR__ . '/Config/define.php';
require_once __DIR__ . '/Core/common.php';
require_once __DIR__ . '/Core/function.php';
require_once __DIR__ . '/Core/runtime.php';

class App{ 
    public function __construct(){
        global $config;
        array_push($config['auto_load_path'], APP_CORE_PATH . "Class/");
        array_push($config['auto_load_path'], APP_CORE_PATH . "Lib/");
        spl_autoload_register("loadClass");
        array_push($config['config_path'], APP_CORE_PATH . 'Config/config.php');
        if(array_key_exists("config_path", $config)){
            foreach ($config['config_path'] as $value) {
                loadConfig($value);
            }
        }
    }
    
    public function run(){
        global $QueryAction, $QueryMethod;
        try {
            @$path_info = trim($_SERVER["PATH_INFO"], "/");
            $arr_info = explode("/", $path_info);
            @$act = lcfirst($arr_info[0] ? $arr_info[0] : "Index");
            $QueryAction = $act;
            $actClass = ucfirst($act . "Action");
            @$method = $arr_info[1] ? $arr_info[1] : "index";
            $QueryMethod = $method;
            $obj = new $actClass();
            if (($obj instanceof Action) && method_exists($obj, $method)) {
                $obj->$method();
                return;
            }else{
                throw new Exception("方法【<b>{$QueryAction}/{$QueryMethod}</b>】不存在");
            }
        } catch (Exception $ex) {
            send_http_status(404);
            die($ex->getMessage());
        }
    }
}

