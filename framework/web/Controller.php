<?php

class Controller{

    // 控制器信息
    private $_module;   // 当前模块名称
    private $_modulePath;   // 当前模块目录
    private $_controller;   // 当前控制器名称
    private $_controllerPath;   // 当前控制器文件路径
    private $_controllerName;   // 当前控制器类名
    private $_actionName;   // 当前控制器执行的方法名
    private $_viewName;     // 当前视图名称 render 的名称
    private $_viewFile;     // 当前视图文件路径

    public $layout;

    /**
     * [__construct 控制器基类构造方法 ]
     * @param array $controllerInfo [ 设置mvc加载实例化文件等基础信息数组 ]
     */
    public function __construct($controllerInfo = array())
    {
        foreach ($controllerInfo as $name => $value) {
            $this->setinfo($name, $value);
        }
    }

    /**
     * [init 控制器构造函数钩子]
     * @return [type] [description]
     */
    public function init()
    {
    }

    /**
     * [ setinfo 设置控制器信息参数 ]
     * @param  [type] $name  [ 参数名 ]
     * @param  [type] $value [ 参数值 ]
     * @return [type]        []
     */
    public function setinfo($name, $value){
        $this->$name = $value;
    }

    /**
     * [ renderout 使用layout的方式渲染视图 对 render 的一个小的升级，将头尾 layout 到另一个框架布局模版中 ]
     * @param  [type] $view   [ 视图渲染文件参数 ]
     * @param  [type] $params [ 向视图传递的参数 ]
     * @return [type]         []
     */
    public function renderout($view,$params=null){
        $viewFile = $this->getVieFile($view);
        if (isset($this->layout) && !empty($this->layout) ) {
            $content = $this->renderFile($viewFile, $params, true);
            $this->render($this->layout, array('content'=>$content));
        }else{
            $this->renderFile($viewFile, $params);
        }
    }

    /**
     * [render 视图渲染与输出 ]
     * @param  [type] $view   [ 视图渲染文件参数。$view格式详见 getVieFile ]
     * @param  [type] $params [ 向视图传递的参数 ]
     * @return [type]         []
     */
    public function render($view, $params = null){
        $viewFile = $this->getVieFile($view);
        $this->renderFile($viewFile, $params);
    }

    /**
     * [getVieFile 获取要加载的视图文件路径 ]
     * @param  [type] $view [ 视图渲染文件参数 ]
     * @return [type]       []
     */
    public function getVieFile($view){
        $view = trim($view);
        if( substr($view, 0, 1) === '/' || strstr($view, '/') ){
            // 如果是 / 开头的 或者包含一个 / 就寻找 本 modules 下面的文件
            return $this->_modulePath . '/view/' . trim($view, '/') . '.php';
        }else{
            // 如果是单文字如：a.php
            return $this->_modulePath . '/view/' . strtolower($this->_controller) . '/' . $view . '.php';
        }
    }


    /**
     * [renderFile 加载视图文件渲染视图核心方法 ]
     * @param  string  $viewFile [ 视图文件系统绝对路径 ]
     * @param  array  $params   [ 向视图文件中传入的参数 ]
     * @param  boolean $return   [ 是否以变量的方式返回字符串 ]
     * @return string            [ 如果 $return 为true 则以字符串的方式返回视图内容 ]
     */
    public function renderFile($viewFile, $params=null, $return=false){
        if( !empty($params) && is_array($params) )
            extract($params,EXTR_PREFIX_SAME,'data');
        if ($return) {
            ob_start();
            // ob_implicit_flush(false);
            require($viewFile);
            return ob_get_clean();
        }else{
            require($viewFile);
        }
    }



}

