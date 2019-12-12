<?php


class CWebApplication extends CApplication {

    // 这里 m 指 modules 里面的模块，而非表名模型
    protected $m = 'm';
    protected $defaultM = 'home';
    protected $v = 'a';
    protected $defaultV = 'index';
    protected $c = 'c';
    protected $defaultC = 'index';

    // application Info
    protected $appInfo;

    // 构造方法
    public function __construct($config){
        // 设置默认模块与获取模块名的参数
        isset($config['main']['defaultModule']) && !empty($config['main']['defaultModule']) && $this->defaultM = $config['main']['defaultModule'];
        isset($config['main']['bindM']) && !empty($config['main']['bindM']) && $this->m = $config['main']['bindM'];
        // 加载整个项目公共方法
        if (isset($config['main']['common']) && !empty($config['main']['common']) && is_array($config['main']['common']) ) {
            foreach ($config['main']['common'] as $key => $path) {
                base::import($path);
            }
        }
        $m = isset($_GET[$this->m]) ? $_GET[$this->m] : $this->defaultM;
        
        // 设置本模块下的默认控制器与默认方法，设置加载控制器参数与加载控制器方法，加载本模块下公共方法
        if( !empty($m) && isset($config['modules'][$m]) ){
            isset($config['modules'][$m]['bindC']) && !empty($config['modules'][$m]['bindC']) && $this->c = $config['modules'][$m]['bindC'];
            isset($config['modules'][$m]['defaultController']) && !empty($config['modules'][$m]['defaultController']) && $this->defaultC = $config['modules'][$m]['defaultController'];
            isset($config['modules'][$m]['bindA']) && !empty($config['modules'][$m]['bindA']) && $this->v = $config['modules'][$m]['bindA'];
            isset($config['modules'][$m]['defaultAction']) && !empty($config['modules'][$m]['defaultAction']) && $this->defaultV = $config['modules'][$m]['defaultAction'];

            // 加载本模块公共方法
            if (isset($config['modules'][$m]['commen']) && !empty($config['modules'][$m]['commen']) && is_array($config['modules'][$m]['commen']) ) {
                foreach ($config['modules'][$m]['commen'] as $key => $path) {
                    base::import($path);
                }
            }
        }
    }

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
            '_m' => $this->m,
            '_v' => $this->v,
            '_c' => $this->c,
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

