<?php


// cookie 或者 session 存储当前会话 加密验证码 还可以在这个加密验证中加入过期时间，表示此页面过期
// form 加一个 字段（经过 加密后产生不同的字符串）经过这个产生解密成 cookie 或者 session 生成的统一字符串 用来验证请求，这个字段经过解密再加密以后跟cookie或者session对比应该是一样的就对了

// 处理请求类 
class CCsrfCookie{

    private $_csrf="_csrf";
    private $_type="cookie";
    private $_scriteKey = "miyao_.!abcded_zifuchuan";
    public $methodParam = '_method';

    public function __construct(){
        $this->init();
    }

    // 请求过来自动设置 csrf
    public function init(){
        if (!$this->haveCsrf()) {
            $this->setCsrf();
        }
        $method = $this->getMethod();
        if ($method==='POST') {
            // 验证是否为 base64 编码
            $formCsrf = base64_encode(base64_decode($_POST[$this->_csrf])) ? $_POST[$this->_csrf] : false;
            $csrf = $this->getCsrf();
            if (empty($formCsrf) || empty($csrf) ) {
                throw new Exception("post missed csrf", 1);
            }else{
                if (!$this->validateCsrf($formCsrf)) {
                    throw new Exception("csrf error", 1);
                }else{
                    unset($_POST[$this->_csrf]);
                }
            }
        }
    }

    // 判断是否请求中含有 csrf
    public function haveCsrf(){
        return isset($_COOKIE[$this->_csrf]) ? true : false;
    }

    // csrf
    public function setCsrf(){
        return setcookie($this->_csrf, base64_encode(uniqid(rand(), true)), 0, "/", "", false, true);
    }

    // csrf
    public function getCsrf(){
        return isset($_COOKIE[$this->_csrf]) ? $_COOKIE[$this->_csrf] : false;
    }

    // make form csrf
    public function formCsrf($overtime=0){
        $cookieCsrf = $this->getCsrf();
        if (empty($cookieCsrf)) {
            return false;
        }
        $overtime = empty($overtime) ? 0 : time() + intval($overtime);
        // 加密函数
        $data = array('overtime'=>$overtime, $this->_csrf=>$cookieCsrf, 'hash'=>md5(rand()));
        return $this->encrypt($data, true, $this->_scriteKey);
    }

    // validate form csrf
    public function validateCsrf($str){
        $str = (string) $str;
        $data = $this->encrypt($str, false, $this->_scriteKey);
        $cookieCsrf = $this->getCsrf();
        if (isset($cookieCsrf) && !empty($cookieCsrf) && isset($data[$this->_csrf]) && !empty($data[$this->_csrf]) && isset($data['overtime']) ) {
            if (empty($data['overtime'])) {
                return $cookieCsrf===$data[$this->_csrf];
            }else{
                return ($cookieCsrf===$data[$this->_csrf]) ? ( ( (empty($data['overtime'])) || $data['overtime']>time() ) ? true : false ) : false;
            }
        }
    }

    /**
     * [encrypt 统一加密解密方法]
     * @param  [type]  $string    [ 待加密/解密的(字符串，数组等都可以) ]
     * @param  boolean $isEncrypt [ true 加密，false 解密 ]
     * @param  [type]  $key       [ 扰码 ]
     * @return [type]             [ 加密/解密后的结果 ]
     */
    function encrypt($string, $isEncrypt = true, $key) {
        $string = $isEncrypt ? serialize($string) : $string;
        if (!isset($string{0}) || !isset($key{0})) {
            return false;
        }
        $dynKey = $isEncrypt ? hash('sha1', microtime(true)) : substr($string, 0, 40);
        $fixedKey = hash('sha1', $key);

        $dynKeyPart1 = substr($dynKey, 0, 20);
        $dynKeyPart2 = substr($dynKey, 20);
        $fixedKeyPart1 = substr($fixedKey, 0, 20);
        $fixedKeyPart2 = substr($fixedKey, 20);
        $key = hash('sha1', $dynKeyPart1 . $fixedKeyPart1 . $dynKeyPart2 . $fixedKeyPart2);

        $string = $isEncrypt ? $fixedKeyPart1 . $string . $dynKeyPart2 : (isset($string{339}) ? gzuncompress(base64_decode(substr($string, 40))) : base64_decode(substr($string, 40)));

        $n = 0;
        $result = '';
        $len = strlen($string);

        for ($n = 0; $n < $len; $n++) {
        $result .= chr(ord($string{$n}) ^ ord($key{$n % 40}));
        }

        return $isEncrypt ? $dynKey . str_replace('=', '', base64_encode($n > 299 ? gzcompress($result) : $result)) : unserialize( substr($result, 20, -20) );
    }

    /**
     * Returns the method of the current request (e.g. GET, POST, HEAD, PUT, PATCH, DELETE).
     * @return string request method, such as GET, POST, HEAD, PUT, PATCH, DELETE.
     * The value returned is turned into upper case.
     */
    public function getMethod()
    {
        if (isset($_POST[$this->methodParam])) {
            return strtoupper($_POST[$this->methodParam]);
        }

        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }


}


