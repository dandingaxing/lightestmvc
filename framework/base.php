<?php

// 引导类
class base{

    // 预定义 类 注册数
    private static $_app;

    // import 加载的类
    private static $_fileMap;

    // 自定义配置文件参数
    private static $_params;

    // 设置项目名称
    private static $_id;

    // 组件默认文件加载配置文件路径
    const componentsConfig = FRAMEWORK_DIR . '/componentsConfig.php';
    // 默认核心文件配置文件路径
    const coreConfig = FRAMEWORK_DIR . '/coreConfig.php';

    // 版本
    public static function getVersion(){
        return '1.0.1';
    }

    // 获取所有app
    public static function getAllApp(){
        return self::$_app;
    }

    // static 内部不可以使用 $this
    /**
     * [run 运行框架]
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    public static function createWebApplication($config){
        // 定义配置文件常量
        if (isset($config['constant']) && !empty($config['constant']) && is_array($config['constant']) ) {
            self::setDefind($config['constant']);
        }
        // 设置配置main基本信息
        if (isset($config['main']) && !empty($config['main']) && is_array($config['main']) ) {
            self::setMainConfig($config['main']);
        }
        // 设置其他参数 params 
        if (isset($config['params']) && !empty($config['params']) && is_array($config['params']) ) {
            self::$_params = $config['params'];
        }

        // 设置并加载核心文件
        self::setCoreClass();
        // 根据配置文件加载并实现特定组件，比如：数据库操作，日志操作，缓存操作等
        self::setComponents($config['components']);

        return self::newCoreClass('CWebApplication', $config);
    }

    // 设置配置文件
    public static function setMainConfig($mainconf){
        // 设置时区
        ( isset($mainconf['timeZone']) && !empty($mainconf['timeZone']) ) && date_default_timezone_set($mainconf['timeZone']);
        // 设置项目名称
        ( isset($mainconf['appid']) && !empty($mainconf['appid']) ) && self::$_id = $mainconf['appid'];
    }

    // 定义常量
    public static function setDefind($constant=null){
        if (!empty($constant)) {
            foreach ($constant as $key => $value) {
                defined($key) or define($key, $value);
            }
        }
    }

    // 实例化核心组件并放到注册树中
    public static function newCoreClass($name, $config=null){
        if (!isset(self::$_app[$name])) {
            self::$_app[$name] = new $name($config);
        }
        return self::$_app[$name];
    }

    // 设置并加载核心文件
    public static function setCoreClass(){
        $coreClass = include(self::coreConfig);
        foreach ($coreClass as $name => $path) {
            self::import($path);
        }
    }

    // 全局组件 components 调用方法
    public static function app($components=null){
        if (empty($components)) {
            return self::$_app;
        }else{
            if (isset(self::$_app[$components])) {
                return self::$_app[$components];
            }else{
                return self::setComponents($components);
            }
        }
    }

    // 设置配置文件组件-并放入注册数中
    public static function setApp($appName, $filePath, $config=array(), $params=array()){
        self::import($filePath);
        $className = basename($filePath, '.php');
        if (isset(self::$_app[$appName])) {
            return self::$_app[$appName];
        }else{
            self::$_app[$appName] = new $className($config);
        }
    }


    // 类 文档加载方法，支持单个文件加载 / 目录加载
    public static function import($filePath){
        if (is_dir($filePath)) {
            $odir = opendir($filePath);
            while ( ($file = readdir($odir)) !== false ) {
                $sub_path = $filePath . DIRECTORY_SEPARATOR . $file;
                if($file == '.' || $file == '..') {
                    continue;
                }elseif(is_dir($sub_path)) {    //如果是目录,进行递归
                    self::import($sub_path);
                }if (is_file($sub_path) && !isset(self::$_fileMap[$sub_path])) {
                    include_once($sub_path);
                    self::$_fileMap[basename($sub_path, '.php')] = $sub_path;
                }
            }
            closedir($odir);
        }elseif (is_file($filePath) && file_exists($filePath) && !isset(self::$_fileMap[$filePath]) ) {
            include_once($filePath);
            self::$_fileMap[basename($filePath, '.php')] = $filePath;
        }
    }


    // 加载组件配置文件
    public static function setComponents( $components = array() ){
        $DC = include( self::componentsConfig );

        foreach ($components as $name => $data) {
            if (empty($data['name'])) continue;
            $dataName = explode('/', $data['name']);

            // 加载系统组件整体基本类
            if (isset($dataName[0]) && isset($DC[$dataName[0]]) ) {
                foreach ($DC[$dataName[0]]['classMap'] as $mapName => $filePath) {
                    self::import($filePath);
                }
            }
            // 加载系统单独功能组件功能类
            if (isset($dataName[1]) && isset($DC[$dataName[0]]['components'][$dataName[1]]) ) {
                foreach ($DC[$dataName[0]]['components'][$dataName[1]]['classMap'] as $mapName => $filePath) {
                    self::import($filePath);
                }
            }
            // 加载用户自定义类
            if(isset($data['classMap'])){
                foreach ($data['classMap'] as $mapName => $filePath) {
                    self::import($filePath);
                }
            }
            // 实例化用户自定义组件入口类
            if (isset($data['newClass']) && file_exists($data['newClass']) ) {
                if (isset($dataName[1]) && isset($DC[$dataName[0]]['components'][$dataName[1]]['newClass'])) {
                    self::import($DC[$dataName[0]]['components'][$dataName[1]]['newClass']);
                }
                $data['construct'] = isset($data['construct']) ? $data['construct'] : null;
                $data['params'] = isset($data['params']) ? $data['params'] : null;
                self::setApp($name, $data['newClass'], $data['construct'], $data['params']);
            }else{
                // 实例化组件入口类
                if (isset($dataName[1]) && isset($DC[$dataName[0]]['components'][$dataName[1]]['newClass'])) {
                    $data['construct'] = isset($data['construct']) ? $data['construct'] : null;
                    $data['params'] = isset($data['params']) ? $data['params'] : null;
                    self::setApp($name, $DC[$dataName[0]]['components'][$dataName[1]]['newClass'], $data['construct'], $data['params']);
                }
            }


        }


    }


    public static function error($msg='error', $code=1){
        throw new Exception($msg, $code);
    }

    public static function getParams(){
        return self::$_params;
    }

    public static function getFileMap(){
        return self::$_fileMap;
    }




























}


