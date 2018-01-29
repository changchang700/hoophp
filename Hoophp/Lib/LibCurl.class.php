<?php
/**
 * @author Mumu
 * @date 2017年8月4日17:29:29
 */
class LibCurl {
    private $errno;
    private $errmsg;
    /**
     * 取请求错误代码
     * @return string
     */
    public function errno() {
        return $this->errno;
    }
    /**
     * 取请求错误信息
     * @return string
     */
    public function errmsg() {
        return $this->errmsg;
    }
    /**
     * 用GET方法请求url
     * @param string $url 要请求的url
     * @return string 返回请求结果，如果请求失败返回false.
     */
    public function get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        $this->errno = curl_errno($ch);
        if ($this->errno) {
            $content = false;
            $this->errmsg = curl_error($ch);
        }
        curl_close($ch);
        return $content;
    }
    /**
     * 用POST方法请求url
     * @param string $url 要请求的url
     * @param mixed $param 请求参数，可以是 array(key=>value) 数组, 也可以是 key=value&key=value 格式的字符串.
     * @return string 返回请求结果，如果请求失败返回false.
     */
    public function post($url, $param = null) {
        $ch = curl_init();
        curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $param, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 10));
        $content = curl_exec($ch);
        $this->errno = curl_errno($ch);
        if ($this->errno) {
            $content = false;
            $this->errmsg = curl_error($ch);
        }
        curl_close($ch);
        return $content;
    }
    
    /**
     * 远程下载文件到服务器
     * @param type $url 需要下载的文件地址
     * @return boolean 是否成功
     */
    public function download($url) {
        $server = config("upload_server");
        $sign = config("upload_sign");
        
        $param["sign"] = $sign;
        $param["action"] = 'download';
        $param["url"] = $url;
        
        $file_url = $this->post($server,$param);
        return $file_url;
    }
}