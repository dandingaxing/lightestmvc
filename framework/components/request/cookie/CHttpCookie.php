<?php
/**
 * CHttpCookie class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * A CHttpCookie instance stores a single cookie, including the cookie name, value, domain, path, expire, and secure.
 *
 * base::app('cookie')->set($cookieName, $cookieValue, $expries);
 * base::app('cookie')->get($cookieName);
 * base::app('cookie')->setDomain('web.com')->setPath('/home')->set($cookieName, $cookieValue, $expries);
 * base::app('cookie')->setDomain('web.com')->setPrefix('abc')->setPath('/home')->set($cookieName, $cookieValue, $expries);
 * base::app('cookie')->setDomain('web.com')->setPrefix('abc')->authSet($cookieName, $cookieValue, $expries);
 * base::app('cookie')->setDomain('web.com')->setPrefix('abc')->authGet($cookieName);
 * 
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.web
 * @since 1.0
 */
class CHttpCookie
{
    // 前缀
    private $_prefix="";

    // 加密扰码
    private $_securekey = 'ekO@!t4_Ut0f3XE-fJ506jyALmTPZ.qp59Ln';   // encrypt key

    /**
     * @var string domain of the cookie
     */
    public $domain='';
    /**
     * @var integer the timestamp at which the cookie expires. This is the server timestamp. Defaults to 0, meaning "until the browser is closed".
     */
    public $expire=0;
    /**
     * @var string the path on the server in which the cookie will be available on. The default is '/'.
     */
    public $path='/';
    /**
     * @var boolean whether cookie should be sent via secure connection
     */
    public $secure=false;
    /**
     * @var boolean whether the cookie should be accessible only through the HTTP protocol.
     * By setting this property to true, the cookie will not be accessible by scripting languages,
     * such as JavaScript, which can effectly help to reduce identity theft through XSS attacks.
     * Note, this property is only effective for PHP 5.2.0 or above.
     */
    public $httpOnly=false;

    // 设置cookie
    public function set($name, $value="", $expire=0){
        return $name && setcookie($this->makeName($name), $value, $expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
    }

    // 获取cookie
    public function get($name){
        return isset($_COOKIE[$this->makeName($name)]) ? $_COOKIE[$this->makeName($name)] : NULL;
    }

    // 清除cookie
    public function clear($name, $prefix=true){
        return $prefix ? setcookie($this->makeName($name), NULL, time()-3600) : setcookie($name, NULL, time()-3600);
    }

    /** 设置前缀
    * @param String $prefix cookie prefix
    */
    public function setPrefix($prefix){
        if(is_string($prefix) && $prefix!=''){
            $this->_prefix = $prefix;
        }
        return $this;
    }

    // 根据前缀拼接name
    public function makeName($name){
        return $this->_prefix ? $this->_prefix.'_'.$name : $name;
    }

    // 带有加密方式的cookie设置
    public function authSet($name, $value="", $expire=0){
        return $name && setcookie($this->makeName($name), $this->authcode($value), $expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
    }

    // 带有加密方式的cookie获取
    public function authGet($name){
        return isset($_COOKIE[$this->makeName($name)]) ? $this->authcode($_COOKIE[$this->makeName($name)], 'DECODE') : NULL;
    }

    // 清楚网站所有cookie
    public function clearAll(){
        foreach(array_keys($_COOKIE) as $key){
            $this->clear($name, false);
        }
    }

    public function setDomain($domain){
        $this->domain = $domain;
        return $this;
    }

    public function setExpire($expire){
        $this->expire = $expire;
        return $this;
    }

    public function setPath($path){
        $this->path = $path;
        return $this;
    }

    public function setSecure($secure){
        $this->secure = $secure;
        return $this;
    }

    public function setHttpOnly($httpOnly){
        $this->httpOnly = $httpOnly;
        return $this;
    }

    public function getDomain($domain){
        return $this->domain;
    }

    public function getExpire(){
        return $this->expire;
    }

    public function getPath(){
        return $this->path;
    }

    public function getSecure(){
        return $this->secure;
    }

    public function getHttpOnly(){
        return $this->httpOnly;
    }

    public function getPrefix(){
        return $this->_prefix;
    }


    /** 加密/解密数据
    * @param  String $str       原文或密文
    * @param  String $operation ENCODE or DECODE
    * @return String            根据设置返回明文活密文
    */
    private function authcode($string, $operation = 'DECODE'){

        $ckey_length = 4;   // 随机密钥长度 取值 0-32;

        $key = $this->_securekey;

        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }

    }





}
