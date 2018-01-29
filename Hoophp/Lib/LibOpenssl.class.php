<?php
/**
 * PHP接口开发使用openssl rsa加密解密
 * 低版本请用其他类
 * @下载地址
 * @date 2017年8月4日09:50:08
 * @author Mumu
 */
class LibOpenssl {
    /**      
     * 私钥加密
     * @param string $data 需要加密的数据
     * @param string $private_key 私钥
     * @return null|string
     */
    public function privEncrypt($data, $private_key) {
        openssl_private_encrypt($data, $encrypted, $private_key);
        $data = base64_encode($encrypted);
        $data = str_replace("/", "@@@@", $data);
        $data = str_replace("+", "$$$$", $data);
        return $data;
    }
    /**      
     * 公钥加密
     * @param string $data 需要加密的数据
     * @param string $public_key 公钥
     * @return null|string
     */
    public function publicEncrypt($data, $public_key) {
        openssl_public_encrypt($data, $encrypted, $public_key);
        $data = base64_encode($encrypted);
        $data = str_replace("/", "@@@@", $data);
        $data = str_replace("+", "$$$$", $data);
        return $data;
    }
    /**      
     * 私钥解密
     * @param string $encrypted 密文
     * @param string $private_key 私钥
     * @return null|string
     */
    public function privDecrypt($encrypted, $private_key) {
        $encrypted = str_replace("$$$$", "+", $encrypted);
        $encrypted = str_replace("@@@@", "/", $encrypted);
        $encrypted = base64_decode($encrypted);
        openssl_private_decrypt($encrypted, $decrypted, $private_key);
        return $decrypted;
    }
    /**      
     * 公钥解密
     * @param string $encrypted 密文
     * @param string $public_key 公钥
     * @return null|string
     */
    public function publicDecrypt($encrypted, $public_key) {
        $encrypted = str_replace("$$$$", "+", $encrypted);
        $encrypted = str_replace("@@@@", "/", $encrypted);
        
        $encrypted = base64_decode($encrypted);
        openssl_public_decrypt($encrypted, $decrypted, $public_key);
        return $decrypted;
    }
    
    public function encrypt($data){
        $data = openssl_encrypt($data,'des-ede3',"123123",0);
        $data = str_replace("/", "@@@@", $data);
        $data = str_replace("+", "$$$$", $data);
        return $data;
    }
    public function decrypt($data){
        $data = str_replace("$$$$", "+", $data);
        $data = str_replace("@@@@", "/", $data);
        $data = base64_decode($data);
        $data = openssl_decrypt($data,'des-ede3',"123123",OPENSSL_RAW_DATA);
        return $data;
    }
}
