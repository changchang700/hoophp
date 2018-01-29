<?php
/*
 * memcache类
  */
class LibMemcache{
    //声明静态成员变量
    private static $m = null;
    
    public function __construct() {
        self::$m = new Memcache();
        self::$m->connect('127.0.0.1','11211');
    }
    
    /*
     * 加入缓存数据
     * @param string $key 获取数据唯一key
     * @param String||Array $value 缓存数据
     * @param $time memcache生存周期(秒)
     */
    public function set($key,$value,$time=0){
        self::$m->set($key,$value,0,$time);
    }
    /*
     * 获取缓存数据
     * @param string $key
     * @return
     */
    public function get($key){
        return self::$m->get($key);
    }
    /*
     * 删除相应缓存数据
     * @param string $key
     * @return
     */
    public function del($key){
        self::$m->delete($key);
    }
    /*
     * 删除全部缓存数据
     */
    public function flush(){
        self::$m->flush();
    }
    
    /**
     * 获取缓存状态
     * @return type
     */
    public function status(){
        return self::$m->getStats();
    }
    
    public function __destruct() {
        self::$m->close();
    }
}