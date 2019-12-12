<?php

// class indexController extends Controller{
// class indexController extends baseController{
class indexController extends homeBaseController{

    // 初始化构造方法
    public function init(){
        parent::init();
        echo "<br>init<br>";
    }

    public function index(){
        echo "<pre>";
        $formCsrf = base::app('csrf')->formCsrf();
        echo $formCsrf;
        echo "<br><br>";
        $abc = base::app('csrf')->validateCsrf($formCsrf);
        var_dump($abc);

        // print_r(base::getFileMap());
        // echo "homepage";

        // base::app('session')->name('')->img();

        // base::app('img')->erweima()->session();

        // base::app('cookie')

        // base::app('session')->validate()->set('');

        // // 图片处理 组件依赖（session）
        // base::app('img')->getverfiy()->set('');

        // base::app('img')->getwater()->set('');



    }


}
