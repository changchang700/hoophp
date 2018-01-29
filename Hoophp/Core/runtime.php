<?php
/**
 * 系统核心文件
 * @author Mumu
 * @date 2017年8月7日09:19:42
 */
/**
 * 自动加载类文件
 * @param type $class
 */
function loadClass($class){
    global $config;
    foreach ($config['auto_load_path'] as $value){
        $paths = array_merge($config['auto_load_path'],subdirs($value));
        $config['auto_load_path'] = $paths;
    }
    foreach ($config['auto_load_path'] as $value) {
        $file_path = $value . $class . '.class.php';
        if(file_exists($file_path)){
            include_once $file_path;
        }
    }
}
/**
 * 加APP配置文件
 * @global type $config 配置信息
 * @param type $config_file 配置文件
 * @return type
 */
function loadConfig($config_file) {
    global $config;
    if (!is_file($config_file)) {
        return;
    }
    $array = include $config_file;
    if (is_array($array)) {
        $config = array_merge($config, $array);
    }
}