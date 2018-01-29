<?php
/**
 * PHP版本必须大于5.6
 * 低版本请用其他类
 * @下载地址 
 * @date 2017年8月4日09:50:08
 * @author Mumu
 */
class LibDes {
    
    public function encrypt($str, $key){  
        $block = mcrypt_get_block_size('des', 'ecb');  
        $pad = $block - (strlen($str) % $block);  
        $str .= str_repeat(chr($pad), $pad);  
        $data = mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        $ret = base64_encode($data);
        $ret = str_replace("/", "@@@@", $ret);
        $ret = str_replace("+", "$$$$", $ret);
        return $ret;
    }
    
    public function decrypt($str, $key){
        $str = str_replace("$$$$", "+", $str);
        $str = str_replace("@@@@", "/", $str);
        $str = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);  
        $block = mcrypt_get_block_size('des', 'ecb');  
        $pad = ord($str[($len = strlen($str)) - 1]);  
        return substr($str, 0, strlen($str) - $pad);  
    }
}
