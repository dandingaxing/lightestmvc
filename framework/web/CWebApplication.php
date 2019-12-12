<?php


class CWebApplication extends CApplication {

    // 这里 m 指 modules 里面的模块，而非表名模型
    protected $m = 'm';
    protected $defaultM = 'home';
    protected $v = 'v';
    protected $defaultV = 'index';
    protected $c = 'c';
    protected $defaultC = 'index';

    // application Info
    protected $appInfo;

    public function run(){
        $this->application();
    }

    // 处理请求并加上请求前后的钩子
    public function application(){
        // 请求处理前的钩子
        $this->beginRequest();
        // 处理请求，实例化 Controller 类
        $this->processRequest();
        // 请求处理后的钩子
        $this->endRequest();
    }

    // 处理请求，实例化 Controller 类
    public function processRequest(){
        $controller = $this->getController();
    }

    // Controller 文件引入与实例化
    public function getController(){
        $controllerInfo = $this->getControllerInfo();
        include_once( $controllerInfo['_controllerPath'] );
        $controller = new $controllerInfo['_controllerName']($controllerInfo);
        // 执行 init 钩子
        $controller->init();

        // 执行 controller 中的 view 方法
        call_user_func_array(array($controller, $controllerInfo['_actionName']), array());

    }

    // 获取 Controller 文件目录位置
    public function getControllerInfo($hz = '.php'){
        $m = isset($_GET[$this->m]) ? $_GET[$this->m] : $this->defaultM;
        $c = isset($_GET[$this->c]) ? $_GET[$this->c] : $this->defaultC;
        $v = isset($_GET[$this->v]) ? $_GET[$this->v] : $this->defaultV;
        return array(
            '_module' => $m,
            '_modulePath' => MODULES_DIR . '/' . $m,
            '_controller' => $c,
            '_controllerPath' => MODULES_DIR . '/' . $m . '/controller/' . $c . 'Controller.php',
            '_controllerName' => $c . 'Controller',
            '_actionName' => $v,
            );
    }

    // 开始请求钩子
    public function beginRequest(){

    }

    // 结束请求钩子
    public function endRequest(){

    }




}

