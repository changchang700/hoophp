<?php
/**
 * @todo 入口文件
 ==============
 * @author Mumu
 * @date 2017年8月3日13:38:05
 */
//ini_set("display_errors", "On");
//error_reporting(E_ALL | E_STRICT);
session_start();
error_reporting(E_ALL^E_NOTICE);
//定义整个应用根目录
define("APP", __DIR__.'/');

//定义应用配置信息
================
$config = array(
                "auto_load_path"=>array(
                    APP . "0_Lib/",
                    APP . "1_Action/",
                    APP . "2_Client/",
                    APP . "3_Common/",
                    APP . "4_Config/"
                ),
                "config_path"=>array(
                    APP . "4_Config/config.php",
                    APP . "4_Config/define.php",
                    APP . "4_Config/function.php"
                )
);

//运行框架
require APP . 'Hoophp/App.php';
(new App())->run();
