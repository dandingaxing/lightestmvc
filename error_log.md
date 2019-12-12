1. 类里面的静态方法中不可以使用 $this 标签，应为可能 $this 都没有被实例化


2. 类里面不可以使用外部定义的常量与字符串进行拼接，报错如下：
Parse error: syntax error, unexpected '.', expecting ')' in E:\xampp\htdocs\m_youlipin\framework\base.php on line 8

错误代码如下：
class base{
    protected static $app;

    // 配置核心必备组件
    private $_coreClasses=array(
        'CApplication' => FRAMEWORK_DIR . '/base/CApplication.php',  // 错误语法，类中不可以使用外部 define 的变量直接拼接字符串
        'CWebApplication' => FRAMEWORK_DIR . '/web/CWebApplication.php', // 错误语法，类中不可以使用外部 define 的变量直接拼接字符串
    );

}

参考链接：
https://segmentfault.com/q/1010000001664375/a-1020000001701905




3. 系统初始化引导类
   base.php
   YII中 YiiBase.php
   ThinkPHP中 Think.class.php
   都是静态类（ 类里面的成员和变量等都是静态方法和静态变量 ）
   为什么这样应该是为了速度和便于后续的调用吧


4. new 控制器类以后使用如下两个函数来将控制器中的方法执行，并传递参数
   call_user_func_array
   call_user_func
   两个函数的区别



5. extract 函数的使用
   extract($params,EXTR_PREFIX_SAME,'data');
   render 中 视图核心方法
   控制器中使人 render 将变量传递给视图
   可以结合 get_defined_vars 函数查看当前视图中所有参数和值


6. ob_implicit_flush
   require(视图文件) 的时候 有时候会用到 ob 函数，可是YII 在使用 ob函数的时候 还单独设置了 ob_implicit_flush 不清楚目的是什么
   YII 代码如下：CBaseController.php
    public function renderInternal($_viewFile_,$_data_=null,$_return_=false)
    {
        // we use special variable names here to avoid conflict when extracting data
        if(is_array($_data_))
            extract($_data_,EXTR_PREFIX_SAME,'data');
        else
            $data=$_data_;
        if($_return_)
        {
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        }
        else
            require($_viewFile_);
    }   


7. 实现mvc的调配以后，就要想到常用的组件（比如封装数据库，缓存，日志，调试等操作的类）加载与使用的问题
   而这些组件有经常不是一个单个文件与类就实现的，通常他们还有各种继承与引用
   因此 在 new 组件 的时候还要 require/include 他所继承的所有类和文件等，
   注：继承的类文件并不需要单独new 一次，只需要 require 或者 include 进来就可以了
   即：只需要加载所有子级类文件就可以 new 这个组件了

   如何实现还是一个问题。。。。









