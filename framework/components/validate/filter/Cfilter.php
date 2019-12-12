<?php

// 过滤类 主要返回 过滤安全后的字符串
class Cfilter{

    // addslashes() 函数返回在预定义字符之前添加反斜杠的字符串。 预定义字符是： 单引号（'） 双引号（"） 反斜杠（\） NULL
    // 注释：默认地，PHP 对所有的 GET、POST 和 COOKIE 数据自动运行 addslashes()。所以您不应对已转义过的字符串使用 addslashes()，因为这样会导致双层转义。遇到这种情况时可以使用函数 get_magic_quotes_gpc() 进行检测。
    public function addslashes($data, $fouce=false){
        if ($fouce) {
            return addslashes($data);
        }
        return get_magic_quotes_gpc() ? $data : addslashes($data);
    }

    // 去除转义字符
    public function stripslashes($data){
        return stripslashes($data);
    }

    // nl2br 在字符串中的新行（\n）之前插入换行符：
    public function nl2br($data){
        return nl2br($data);
    }

    // 去除html并转义
    public function delHtml($data){
        return htmlspecialchars( strip_tags($data) );
    }

    public function nrhtml($data){
        return htmlspecialchars($data);
    }

    // 换行转 br
    public function nr2br($data){
        return str_replace(array('\r\n', '\r', '\n'), chr(13), $data);
    }

    public function quotemeta($data){
        return quotemeta($data);
    }

    public function htmlentities($data){
        return htmlentities($data);
    }

    // mysql 插入前转义（尽量使用 pdo 进行操作而不使用词此方法）
    // Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.
    public function filtermysql(){
    }

    public function url($url){
        return urlencode($url);
    }

    // 过滤器删除字符串中所有非法的 e-mail 字符 删除所有字符，除了字母、数字以及 !#$%&'*+-/=?^_`{|}~@.[]
    public function filterEmail($data){
        return filter_var($data, FILTER_SANITIZE_EMAIL);
    }

    // FILTER_SANITIZE_URL 过滤器删除字符串中所有非法的 URL 字符。
    // 该过滤器允许所有的字符、数字以及 $-_.+!*'(),{}|\\^~[]`"><#%;/?:@&=。
    public function filterUrl($data){
        return filter_var($data, FILTER_SANITIZE_URL);
    }

    //删除空格
    function trimall($str)//删除空格
    {
        return str_replace(array(" ","　","\t","\n","\r"),"",$str);
    }

    // 只剩下 0-9 a-z A-Z . - _ 的值
    public function getw($str){
        return $this->pregReplace('/[^0-9a-zA-Z.-_]+/', '', $str);
    }

    // 只剩下 0-9 a-z A-Z
    public function getEnstr($str){
        return $this->pregReplace('/[^0-9a-zA-Z]+/','',$str);
    }

    // 只剩下数字字母和汉字
    public function getZhstr($str){
        return $this->pregReplace('/[^0-9a-zA-Z\x{4e00}-\x{9fa5}]+/u','',$str);
    }

    // 去掉最后字符
    public function pregReplace($pattern, $replacement, $subject){
        return preg_replace($pattern, $replacement, $subject);
    }

    // 去掉多余回车
    public function delMorebs($str){
        return $this->pregReplace("/\s+/", " ", $str); //过滤多余回车
    }


    // 获取图片文件名称和后缀
    public function imgFileSuffix($fileStr){
        $array  = array();
        $array['houzhui'] = strtolower(substr(strrchr($fileStr, '.'), 1));
        $array['wenjian'] = substr($fileStr, 0, strrpos($fileStr, '.'));
        return $array;
    }


















}

