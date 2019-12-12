<?php

// 处理请求类 
class CRequest{

    private $_csrf;
    private $is_csrf = true;

    public function __construct(){
        $this->init();
    }

    // 
    public function init(){
        if ($this->is_csrf) {

        }
    }

    // csrf
    public function setCsrf(){
    }

    // csrf
    public function getCsrf(){
    }

    // 验证 csrf
    public function validate(){

    }

    // curl get
    public function httpGet(){

    }

    public function httpsGet(){

    }

    public function httpPost(){

    }

    public function jsonPost(){

    }

    public function httpAuthPost(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jifenguanli.youlipin.com/oauth/accesstoken");  
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('grant_type' => 'client_credentials'));
        curl_setopt($ch, CURLOPT_USERPWD, 'demo_clientid:demo_password');
        $result=curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }







}


