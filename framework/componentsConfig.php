<?php

// 组件基础配置文件
// 组件可分两级，每一级都是一个单独的组件包
return array(

    // 抽象总类组件[ 数据库操作分支 ]
    'db' => array(
        
        'components' => array(

            // 单独功能组件
            'mysql' => array(
                'newClass' => FRAMEWORK_DIR . '/components/db/mysql/mysqlDB.php',
                // classMap 是有加载的先后顺序的，必须最基础的类先加载
                'classMap' => array(
                    'mysqlConnect' => FRAMEWORK_DIR . '/components/db/mysql/lib/mysqlConnect.php',
                    'mysqlBuilder' => FRAMEWORK_DIR . '/components/db/mysql/lib/mysqlBuilder.php',
                ),
            ),

            'mysqli' => array(
                'newClass' => FRAMEWORK_DIR . '/components/db/mysqli/mysqliDB.php',
                // classMap 是有加载的先后顺序的，必须最基础的类先加载
                'classMap' => array(
                    'mysqliConnect' => FRAMEWORK_DIR . '/components/db/mysqli/lib/mysqliConnect.php',
                    'mysqliBuilder' => FRAMEWORK_DIR . '/components/db/mysqli/lib/mysqliBuilder.php',
                ),
            ),

            'mysqlpdo' => array(
                'newClass' => FRAMEWORK_DIR . '/components/db/mysqlpdo/pdoDB.php',
                // classMap 是有加载的先后顺序的，必须最基础的类先加载
                'classMap' => array(
                    'pdoConnect' => FRAMEWORK_DIR . '/components/db/mysqlpdo/lib/pdoConnect.php',
                    'pdoBuilder' => FRAMEWORK_DIR . '/components/db/mysqlpdo/lib/pdoBuilder.php',
                ),
            ),

            // 单独功能组件
            'redis' => array(
                'newClass' => FRAMEWORK_DIR . '/components/db/redis/MyRedis.php',
                'classMap' => array(
                ),
            ),

        ),

        // 整体组件基础公共文件
        'classMap' => array(
        ),
    ),

    // 抽象总类组件[ 请求操作分支 ]
    'request' => array(

        'components' => array(
            // 单独功能组件
            'session' => array(
                'newClass' => FRAMEWORK_DIR . '/components/request/CRequest.php',
                'classMap' => array(
                ),
            ),

            // 使用 redis 存储session
            'sessionRedis' => array(
                'newClass' => FRAMEWORK_DIR . '/components/request/sessionRedis/CRedisSession.php',
                'classMap' => array(
                ),
            ),

            // 使用 mysql 存储redis
            'sessionMysql' => array(
                'newClass' => FRAMEWORK_DIR . '/components/request/sessionMysql/CDbSession.php',
                'classMap' => array(
                ),
            ),

            // cookie 操作
            'cookie' => array(
                'newClass' => FRAMEWORK_DIR . '/components/request/cookie/CHttpCookie.php',
                'classMap' => array(
                ),
            ),

            // csrf 验证
            'csrf' => array(
                'newClass' => FRAMEWORK_DIR . '/components/request/csrf/CCsrfCookie.php',
                'classMap' => array(
                ),
            ),


        ),
        'classMap' => array(
        ),
    ),

    // 图片处理类
    'image' => array(
        'components' => array(
            // 图片生成类
            'makepic' => array(
                'newClass' => FRAMEWORK_DIR . '/components/img/makepic/imgMake.php',
                'classMap' => array(
                ),
            ),
            // 图片处理类 - 缩略 - 水印 - 裁切 - 翻转 等
            'imgHandle' => array(
                'newClass' => FRAMEWORK_DIR . '/components/img/imghandle/imgHandle.php',
                'classMap' => array(
                    'ThumbHandler' => FRAMEWORK_DIR . '/components/img/imghandle/lib/ThumbHandler.php',
                ),
            ),
            // 图片/文件 本地上传类
            'upload' => array(
                ''
            ),
        ),
        'classMap' => array(
        ),
    ),

    // 页面构建[ 页面分支 ]
    'webpage' => array(
        'components' => array(
            // 分页
            'page' => array(
                'newClass' => FRAMEWORK_DIR . '/components/webpage/page/Cpage.php',
                'classMap' => array(
                ),
            ),
        ),
        'classMap' => array(
        ),
    ),








);

