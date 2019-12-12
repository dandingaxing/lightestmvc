<?php

return array(

    // 组件
    'components' => array(
        
        // 使用 mysql
        'mysql' => array(
            // 
            'name' => 'db/mysqlpdo',
            // 类 初始化 公共参数
            'params' => array(
                ),
            // 类 构造方法传入，初始化构建参数
            'construct' => array(
                'host' => 'mysql-host',
                'user' => 'mysqluser',
                'password' => 'mysqlpasswd',
                'database' => 'mysqldbname',
                'charset' => 'utf8',
                'port' => 3306,
                ),

            // 可自定义组件与配置
            // 'newClass' => EXTENSION_DIR . '/components/db/mysql/ourmysql.php',
            // classMap 是有加载的先后顺序的，必须最基础的类先加载
            // 'classMap' => array(
            //     'mysqlConnect' => FRAMEWORK_DIR . '/components/db/mysql/lib/mysqlConnect.php',
            //     'mysqlBuilder' => FRAMEWORK_DIR . '/components/db/mysql/lib/mysqlBuilder.php',
            // ),

        ),

        // 自定义组件
        'mycomp' => array(
            'name' => 'mycomp',
            'construct' => array(
                'democomp__abc_1' => 'democomp__abc_1',
                'democomp__abc_2' => 'democomp__abc_2',
                'democomp__abc_3' => 'democomp__abc_3',
            ),
            'newClass' => EXTENSION_DIR . '/components/mycomp/mycomp.php',
            'classMap' => array(
                'basemycomp' => EXTENSION_DIR . '/components/mycomp/lib/basemycomp.class.php',
            ),
        ),


        // 使用 redis
        'redis' => array(
            'name' => 'db/redis',
            'construct' => array(
                'host' => '127.0.0.1',
                'port' => 6379,
                'auth' => '',
            ),
        ),

        // 使用原生 session
        // 'session' => array(
        //     'name' => 'request/session',
        // ),

        // 使用mysql 做session
        'sessionMysql' => array(
            'name' => 'request/sessionMysql',
            'construct' => array(
                'host' => 'mysql-host',
                'user' => 'mysqluser',
                'password' => 'mysqlpasswd',
                'database' => 'mysqldbname',
                'charset' => 'utf8',
                'port' => 3306,
                ),
        ),

        // 使用redis 操作 session
        'sessionRedis' => array(
            'name' => 'request/sessionRedis',
        ),

        // 使用 cookie
        'cookie' => array(
            'name' => 'request/cookie',
        ),

        // 使用 csrf 请求验证
        'csrf' => array(
            'name' => 'request/csrf',
        ),

        // 使用 page 类
        'page' => array(
            'name' => 'webpage/page',
        ),

        // 验证码
        'verify' => array(
            'name' => 'image/makepic',
        ),



    ),
    
    // 基础配置
    'main' => array(
        'appid' => 'demo项目',
        'language' => 'zh-cn',
        'timeZone'=>'Asia/Shanghai',
        // 公共方法
        'common' => array(
            ROOT_DIR . '/common',
        ),
        // 默认模块名称
        'defaultModule' => 'home',
        // 默认模块加载参数
        'bindM' => 'm',
    ),

    // 自定义常量
    'constant' => array(
        'COMMENT' => ROOT_DIR . '/comment',
    ),

    // 模块设置
    'modules' => array(

        // home 模块设置
        'home' => array(
            // 默认控制器类
            'defaultController' => 'index',
            // 控制器加载参数
            'bindC' => 'c',
            // 默认方法
            'defaultAction' => 'index',
            // 方法加载参数
            'bindA' => 'a',
            // 默认错误处理类
            'errorController' => 'error',
            // 本模块下公共方法目录
            'commen' => array(
                MODULES_DIR . '/home/common',
            ),
        ),

        // admin 模块设置
        'admin' => array(
            // 默认控制器类
            'defaultController' => 'index',
            // 控制器加载参数
            'bindC' => 'c',
            // 默认方法
            'defaultAction' => 'index',
            // 方法加载参数
            'bindA' => 'a',
            // 默认错误处理类
            'errorController' => 'error',
            // 本模块下公共方法目录
            'commen' => array(
                MODULES_DIR . '/admin/common',
            ),
        ),

    ),


    // 自定义配置
    'params' => array(
        
    ),


    

);


