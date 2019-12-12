<?php

// 验证类 主要返回 true 或者 false
class Cvalidate{
    
    /**
     * 是否为空值
     */  
    public function isEmpty($str){
        $str = trim($str);
        return empty($str) ? true : false;
    }

    public function isEmail($data){
        return filter_var($data, FILTER_VALIDATE_EMAIL);
    }

    public function pregEmail($data){
        return $this->preg("/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i",$data) ? true : false;
    }

    public function isUrl($data){
        return filter_var($data, FILTER_VALIDATE_URL);
    }

    public function pregUrl($data){
        return $this->preg('#(http|https|ftp|ftps)://([w-]+.)+[w-]+(/[w-./?%&=]*)?#i',$data) ? true : false;
    }

    public function isIp($data){
        return filter_var($data, FILTER_VALIDATE_IP);
    }

    /**
     * [intMinmax 包含 min 与 max 的值的区间， ]
     * @param  [type] $data  [description]
     * @param  [type] $min   [description]
     * @param  [type] $max   [description]
     * @param  string $flags [ 为空为10进制，FILTER_FLAG_ALLOW_HEX 16进制，FILTER_FLAG_ALLOW_OCTAL 10进制 ]
     * @return [type]        [ $data / false ]
     */
    public function intMinmax($data, $min, $max, $flags=""){
        return filter_var($data, FILTER_VALIDATE_INT, $options = array( 'options' => array('min_range' => $min, 'max_range' => $max ), 'flags' => $flags ));
    }

    // 手机
    public function isPhone($data){
        return $this->preg('/^((\+86)|(86))?(1)[3456789]{1}\d{9}$/', $data);
    }

    // 电话
    public function isTelephone($data){
        return $this->preg('/^([0-9]{3,4}-)?[0-9]{7,8}$/', $data);
    }

    // 身份证
    public function isIdcard($data){
        return $this->preg('/(^d{15}$)|(^d{17}(d|X|x)$)/', $data);
    }

    // QQ
    public function isQQ($data){
        return $this->preg('/^[1-9][\d]{4,10}$/', $data);
    }

    /**
     * 验证长度
     * @param: string $str
     * @param: int $type(方式，默认min <= $str <= max)
     * @param: int $min,最小值;$max,最大值;
     * @param: string $charset 字符
    */
    public function length($str,$min=0,$max=0,$type=3,$charset = 'utf-8'){
        $len = mb_strlen($str,$charset);
        switch($type){
            case 1: //只匹配最小值
                return ($len >= $min) ? true : false;
                break;
            case 2: //只匹配最大值
                return ($max >= $len) ? true : false;
                break;
            default: //min <= $str <= max
                return (($min <= $len) && ($len <= $max)) ? true : false;
        }
    }

    // 字母数字_-.
    public function isChart($data, $minLen=0, $maxLen=){
        return $this->preg('/^[a-zA-Z][a-zA-Z0-9_-.]+$/', $data);
    }

    /**
     * 验证用户名
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public function isNames($value, $minLen=2, $maxLen=16, $charset='ALL'){
        if(empty($value))
            return false;
        switch($charset){
            case 'EN': $match = '/^[_\w\d]{'.$minLen.','.$maxLen.'}$/iu';
                break;
            case 'CN':$match = '/^[_\x{4e00}-\x{9fa5}\d]{'.$minLen.','.$maxLen.'}$/iu';
                break;
            default:$match = '/^[_\w\d\x{4e00}-\x{9fa5}]{'.$minLen.','.$maxLen.'}$/iu';
        }
        return preg_match($match,$value);
    }

    /**
     * 验证中文 全是中文
     * @param:string $str 要匹配的字符串
     * @param:$charset 编码（默认utf-8,支持gb2312）
     */  
    public function isChinese($str, $charset = 'utf-8'){
        if($this->isEmpty($str)) return false;
        $match = (strtolower($charset) == 'gb2312') ? "/^[".chr(0xa1)."-".chr(0xff)."]+$/"
        : '/^[\x{4e00}-\x{9fa5}]+$/u';
        return $this->preg($match, $str) ? true : false;
    }

    // 是否包含中文
    public function haveChinese($str){
        return $this->preg('/[\x{4e00}-\x{9fa5}]/u', $str)>0;
    }

    /* 
     * UTF-8 编码下关于中文

        if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str)>0) {
            echo 'all chinese';
        } else if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $str)>0) {
            echo 'have chinese';
        } else {
            echo 'no chinese';
        }

    */

    /**
     * UTF-8验证
     */      
    public function isUtf8($str){
        if($this->isEmpty($str)) return false;
        return (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word)
        == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word)
        == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word)
        == true) ? true : false;
    }

    /**
     * 验证密码
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public function isPWD($value,$minLen=6,$maxLen=16){
        $match='/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{'.$minLen.','.$maxLen.'}$/';
        if($this->isEmpty($value)) return false;
        return $this->preg($match,$v);
    }


    /**
     * 校验日期格式是否正确
     * 
     * @param string $date 日期
     * @param string $formats 需要检验的格式数组
     * @return boolean
     */
    function isData($date, $formats = array("Y-m-d", "Y-m-d H:i:s", "Y/m/d", "Y/m/d H:i:s")) {
        $unixTime = strtotime($date);
        if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
            return false;
        }
        //校验日期的有效性，只要满足其中一个格式就OK
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }
        return false;
    }


    // 正则
    public function preg($rule, $data){
        return preg_match($rule, $data);
    }



}

