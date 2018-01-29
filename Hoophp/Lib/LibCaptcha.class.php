<?php
class LibCaptcha {
    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
    private $code; //验证码
    private $codelen = 4; //验证码长度
    private $width = 130; //宽度
    private $height = 50; //高度
    private $img; //图形资源句柄
    private $font; //指定的字体
    private $fontsize = 20; //指定字体大小
    private $fontcolor; //指定字体颜色
    /**
     * 构造函数
     */
    public function __construct() {
        @session_start();
        $this->font = dirname(__FILE__) . '/../Resources/ELEPHNTI.TTF'; //注意字体路径要写对，否则显示不了图片

    }
    /**
     * 生成验证码
     */
    private function createCode() {
        $_len = strlen($this->charset) - 1;
        for ($i = 0;$i < $this->codelen;$i++) {
            $this->code.= $this->charset[mt_rand(0, $_len) ];
        }
        $_SESSION['captcha_code'] = strtolower($this->code);
    }
    /**
     * 生成背景
     */
    private function createBg() {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }
    /**
     * 生成字体
     */
    private function createFont() {
        $_x = $this->width / $this->codelen;
        for ($i = 0;$i < $this->codelen;$i++) {
            $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
        }
    }
    /**
     * 生成线条、雪花
     */
    private function createLine() {
        //线条
        for ($i = 0;$i < 6;$i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        //雪花
        for ($i = 0;$i < 100;$i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
    }
    /**
     * 输出图片
     */
    private function outPut() {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }
    /**
     * 对外创建验证码
     */
    public function createImg() {
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }
    /**
     * 对外检查验证码
     * @param type $code
     * @return boolean
     */
    public function checkCode($code){
        if($_SESSION['captcha_code']== strtolower($code)){
            return true;
        }
        return false;
    }
}
