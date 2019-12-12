<?php

// 所有模块的公共控制器
class baseController extends Controller{

    // 初始化构造方法
    public function init(){
        parent::init();
        echo "<br>base controller<br>";
    }


}
