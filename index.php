<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

// 定义网站入口根目录
defined('ROOT_DIR') or define('ROOT_DIR', dirname(__FILE__));

// 定义 framework 框架目录
defined('FRAMEWORK_DIR') or define('FRAMEWORK_DIR', ROOT_DIR . '/framework');

// 定义 modules 所有模块的目录位置
defined('MODULES_DIR') or define('MODULES_DIR', ROOT_DIR . '/modules');

// 定义 debug 开启
defined('PANDA_DEBUG') or define('PANDA_DEBUG',true);

// 定义扩展目录
defined('EXTENSION_DIR') or define('EXTENSION_DIR', ROOT_DIR . '/extension');

// echo "<pre>";

// 获取配置文件
$config = require_once( ROOT_DIR . '/config/main.php' );

require_once( FRAMEWORK_DIR . '/base.php' );

// 运行框架
base::createWebApplication($config)->run();
