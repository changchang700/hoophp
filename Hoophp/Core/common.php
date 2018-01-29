<?php
/**
 * 此文件主要初始化内部类文件
 * @author Mumu
 * @date 2017年8月7日08:58:45
 */
/**
 * 数据库操作
 * @global type $_db
 * @return \Db
 */
function _init_db() {
    static $_db;
    if (!$_db) {
        $_db = new LibDb();
    }
    return $_db;
}
/**
 * memcache操作
 * @staticvar Memcache $_memcache
 * @return \Memcache
 */
function _init_memcache(){
    static $_memcache;
    if (!$_memcache) {
        $_memcache = new LibMemcache();
    }
    return $_memcache;
}
/**
 * des操作
 * @staticvar Des $_des
 * @return \Des
 */
function _init_des(){
    static $_des;
    if (!$_des) {
        $_des = new LibDes();
    }
    return $_des;
}
/**
 * 客户端操作
 * @staticvar Client $_client
 * @return \Client
 */
function _init_client(){
    static $_client;
    if (!$_client) {
        $_client = new LibClient();
    }
    return $_client;
}
/**
 * redis操作
 * @global LibRedis $_redis
 * @return \LibRedis
 */
function _init_redis(){
    static $_redis;
    if (!$_redis) {
        $_redis = new LibRedis();
    }
    return $_redis;
}
/**
 * 分页操作
 * @staticvar LibPage $_page
 * @return \LibPage
 */
function _init_page(){
    static $_page;
    if (!$_page) {
        $_page = new LibPage();
    }
    return $_page;
}
/**
 * 验证码操作
 * @staticvar LibCaptcha $_captcha
 * @return \LibCaptcha
 */
function _init_captcha(){
    static $_captcha;
    if (!$_captcha) {
        $_captcha = new LibCaptcha();
    }
    return $_captcha;
}
/**
 * curl操作
 * @staticvar LibCurl $_curl
 * @return \LibCurl
 */
function _init_curl(){
    static $_curl;
    if (!$_curl) {
        $_curl = new LibCurl();
    }
    return $_curl;
}
/**
 * 文档操作
 * @staticvar LibDoc $_doc
 * @return \LibDoc
 */
function _init_doc(){
    static $_doc;
    if(!$_doc){
        $_doc = new LibDoc();
    }
    return $_doc;
}
/**
 * 验证操作
 * @staticvar LibCheck $_check
 * @return \LibCheck
 */
function _init_check(){
    static $_check;
    if(!$_check){
        $_check = new LibCheck();
    }
    return $_check;
}
/**
 * Rsa加密操作
 * @staticvar LibRsa $_rsa
 * @return \LibRsa
 */
function _init_openssl(){
    static $_openssl;
    if(!$_openssl){
        $_openssl = new LibOpenssl();
    }
    return $_openssl;
}