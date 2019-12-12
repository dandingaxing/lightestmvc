<?php

/**
 * @auhor axing
 * 
 * 查询操作主要是PDO::query()、PDO::exec()、PDO::prepare()
 * PDO::query()主要是用于有记录结果返回的操作，特别是SELECT操作
 * PDO::exec()主要是针对没有结果返回的操作，比如INSERT、UPDATE、DELETE等操作，它返回的结果是当前操作影响的列数
 * PDO::prepare()主要是预处理操作，需要通过$rs->execute()来执行预处理里面的sql语句，这个方法可以绑定参数，功能比较强大 用于 存储过程等
 *
 * 主要参考资料：
 * http://php.net/manual/zh/book.pdo.php
 * http://php.net/manual/zh/pdo.connections.php
 * http://php.net/manual/zh/class.pdo.php
 * http://php.net/manual/zh/class.pdostatement.php
 */

class pdoConnect{

    // 数据库 PDO 链接标识
    private $_CONN = null;
    
    // 数据库格式 mysql
    private $_DBTYPE = 'mysql';
    // 数据库链接地址 127.0.0.1
    private $_DBHOST;
    // 数据库链接端口 3306
    private $_DBPORT;
    // 数据库链接用户名 root
    private $_DBUSER;
    // 数据库链接密码
    private $_DBPASSWORD;
    // 链接使用数据库名称
    private $_DBNAME;
    // 数据库编码 utf8
    private $_DBCHARSET;
    // PDO连接参数
    private $_DBOPTIONS = array(
        PDO::ATTR_CASE              => PDO::CASE_LOWER,                 // 键值强制列名转换为小写
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,          // 设置错误报告为 抛出 exceptions 异常。
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,               // 转换 NULL 和空字符串
        PDO::ATTR_STRINGIFY_FETCHES => false,                           // 关闭提取的时候将数值转换为字符串
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
    // 事务指令数
    private $transTimes = 0;

    /**
     * [ 连接数据库 ]
     * @param  [array] $config = array(
     *                           'host'=>'127.0.0.1',    // 数据库链接地址
     *                           'port'=>'3306',         // 数据库链接端口
     *                           'user'=>'root',         // 数据库链接用户名
     *                           'password'=>'',         // 数据库链接密码
     *                           'database'=>'',             // 链接使用数据库名称
     *                           'charset'=>'utf8',      // 数据库编码
     *                           'options'=>array(),       // pdo连接参数
     *                           )
     */
    public function __construct( $config = array( 'host'=>'127.0.0.1', 'port'=>'3306', 'user'=>'root', 'password'=>'', 'database'=>'', 'charset'=>'utf8', 'options'=>array() ) ){
        $this->_DBHOST = empty($config['host']) ? '127.0.0.1' : $config['host'];
        $this->_DBPORT = empty($config['port']) ? 3306 : intval($config['port']);
        $this->_DBUSER = empty($config['user']) ? 'root' : $config['user'];
        $this->_DBPASSWORD = empty($config['password']) ? '' : $config['password'];
        $this->_DBNAME = empty($config['database']) ? '' : $config['database'];
        $this->_DBCHARSET = empty($config['charset']) ? '' : $config['charset'];
        if (isset($config['options']) && is_array($config['options']) && !empty($config['options']) ) {
            $this->_DBOPTIONS = array_merge( $this->_DBOPTIONS, $config['options'] );
        }
        if (version_compare(PHP_VERSION, '5.3.6', '<=')) {
            // 禁用模拟预处理语句
            $this->_DBOPTIONS[PDO::ATTR_EMULATE_PREPARES] = false;
        }
        // 链接数据库
        $this->connect();
    }

    // 链接数据库
    private function connect(){
        $dsn = $this->_DBTYPE . ':host=' . $this->_DBHOST . ';port=' . $this->_DBPORT . ';dbname=' . $this->_DBNAME . ';charset='.$this->_DBCHARSET;
        try{
            $this->_CONN = new PDO($dsn, $this->_DBUSER, $this->_DBPASSWORD, $this->_DBOPTIONS);
        }catch(PDOException $e){
            exit('pdo mysql error: '.$e->getMessage());
        }
    }

    /**
     * [getDb 获取链接标识]
     * @return [type] [ mysql 连接标识 $this->_conn ]
     */
    public function getDb(){
        return $this->_conn;
    }

    // 返回链接标识
    public function db(){
        return $this->_CONN;
    }

    /**
     * 预编译 存储过程 insert
     * @param  [ string ] $table  [ 表名 ]
     * @param  [ array ] $params = array(
     *                             'colume' => 'value',     // 列名 => 值
     *                             'colume' => 'value',
     *                             'colume' => 'value',
     *                             )
     * @return [ string ]         [ 插入数据后返回的ID ]
     */
    public function baseInsert($table, $params){
        $inkeys = " ( ";
        $invalues = " ( ";
        foreach ($params as $colume => $param) {
            $inkeys .= $colume.', ';
            $invalues .= ':'.$colume.', ';
        }
        $inkeys = substr($inkeys,0,-2) . ' )';
        $invalues = substr($invalues,0,-2) . ' )';
        $insertsql = "INSERT INTO {$table} {$inkeys} VALUES {$invalues}";
        $stmt = $this->_CONN->prepare($insertsql);
        foreach ($params as $colume => $param) {
            $stmt->bindParam(':'.$colume, $params[$colume]);
        }
        $stmt->execute();
        return $this->_CONN->lastInsertId();
    }

    /**
     * 执行任意预编译sql语句
     * @param  [ string ] $sql      [ 要执行的预编译sql语句 ]
     * @param  [ array ] $params    [ 预编译sql语句中参数的值 ]
     * @return [ SQL语句执行对象 ]
     */
    public function execute( $sql, $params ){
        $stmt = $this->_CONN->prepare($sql);
        foreach ($params as $colume => $param) {
            $stmt->bindParam($colume, $params[$colume]);
        }
        return ($stmt->execute()) ? $stmt : false;
    }

    /**
     * 预编译方式查询 多条数据
     * @param  [type] $sql       [ 要执行的预编译sql语句 ]
     * @param  [type] $params    [ 预编译sql语句中参数的值 ]
     * @param  [ string ] $FetchMode [ 数据返回格式 PDO::FETCH_ASSOC | PDO::FETCH_NUM | PDO::FETCH_BOTH | PDO::FETCH_OBJ 等 ]
     * @return [type]                [ 查询返回多条数据结果 （二维数组/对象） ]
     */
    public function baseSelect( $sql, $params, $FetchMode = PDO::FETCH_ASSOC ){
        $stmt = $this->_CONN->prepare($sql);
        foreach ($params as $colume => $param) {
            $stmt->bindParam($colume, $params[$colume]);
        }
        return ($stmt->execute()) ? $stmt->fetchAll($FetchMode) : false;
    }

    /**
     * 预编译方式查询 单条数据
     * @param  [type] $sql       [ 要执行的预编译sql语句 ]
     * @param  [type] $params    [ 预编译sql语句中参数的值 ]
     * @param  [ string ] $FetchMode [ 数据返回格式 PDO::FETCH_ASSOC | PDO::FETCH_NUM | PDO::FETCH_BOTH | PDO::FETCH_OBJ 等 ]
     * @return [type]                [ 查询返回单条数据结果 （一维数组/对象） ]
     */
    public function baseSelectOne( $sql, $params, $FetchMode = PDO::FETCH_ASSOC ){
        $stmt = $this->_CONN->prepare($sql);
        foreach ($params as $colume => $param) {
            $stmt->bindParam($colume, $params[$colume]);
        }
        return ($stmt->execute()) ? $stmt->fetch($FetchMode) : false;
    }

    /**
     * 预编译方式更新或删除
     * @param  [type] $sql       [ 要执行的预编译sql语句 ]
     * @param  [type] $params    [ 预编译sql语句中参数的值 ]
     * @return [int]             [ 返回受影响条数 ]
     */
    private function executeUpDel( $sql, $params ){
        $stmt = $this->_CONN->prepare($sql);
        foreach ($params as $colume => $param) {
            $stmt->bindParam($colume, $params[$colume]);
        }
        return ($stmt->execute()) ? $this->rowCount($stmt) : false;
    }

    /**
     * 预编译方式更新
     * @param  [type] $sql       [ 要执行的预编译sql语句 ]
     * @param  [type] $params    [ 预编译sql语句中参数的值 ]
     * @return [int]             [ 返回受影响条数 ]
     */
    public function baseUpdate( $sql, $params ){
        return $this->executeUpDel( $sql, $params );
    }

    /**
     * 预编译方式更新
     * @param  [type] $sql       [ 要执行的预编译sql语句 ]
     * @param  [type] $params    [ 预编译sql语句中参数的值 ]
     * @return [int]             [ 返回受影响条数 ]
     */
    public function baseDelete( $sql, $params ){
        return $this->executeUpDel( $sql, $params );
    }

    /**
     * 单独 设置PDO属性 预定义常量
     * @param [ array ] $params [ 参数数组 例如： ] array(
     *                                                PDO::ATTR_CASE => PDO::CASE_LOWER,
     *                                                PDO::ATTR_TIMEOUT => 10,
     *                                                ......
     *                                                );
     */
    public function setAttribute( $params ){
        foreach ($params as $key => $param) {
            $this->_CONN->setAttribute($key, $params[$key]);
        }
    }

    // 获取最后插入ID
    public function lastInsertId(){
        return $this->_CONN->lastInsertId();
    }

    /**
     * 获取所有数据
     * @param  [ object ] $stmt      [ SQL语句执行对象 ]
     * @param  [ string ] $FetchMode [ 数据返回格式 PDO::FETCH_ASSOC | PDO::FETCH_NUM | PDO::FETCH_BOTH | PDO::FETCH_OBJ 等 ]
     * @param  string $paramkey      [ 键值key 字段 ]
     * @return [type]                [ 查询返回多条数据结果 （二维数组/对象） ]
     */
    public function fetchAll($stmt, $FetchMode = PDO::FETCH_ASSOC, $paramkey=''){
        if (empty($paramkey)) {
            return $stmt->fetchAll($FetchMode);
        }else{
            $dataList = array();
            while ($row = $stmt->fetch($FetchMode)) {
                if (!empty($paramkey)) {
                    $dataList[$row[$paramkey]] = $row;
                }
            }
            return $dataList;
        }
    }

    /**
     * 获取单条数据
     * @param  [ object ] $stmt      [ SQL语句执行对象 ]
     * @param  [ string ] $FetchMode [ 数据返回格式 PDO::FETCH_ASSOC | PDO::FETCH_NUM | PDO::FETCH_BOTH | PDO::FETCH_OBJ 等 ]
     * @return [type]                [ 查询返回多条数据结果 （二维数组/对象） ]
     */
    public function fetch($stmt, $FetchMode = PDO::FETCH_ASSOC){
        return $stmt->fetch($FetchMode);
    }

    // 返回上一条SQL语句影响的行数
    public function rowCount($stmt){
        return $stmt->rowCount();
    }

    // 从结果集中的下一行返回单独的一列 列的索引数字（以0开始的索引） 如果没有提供值则获取第一列
    public function fetchColumn($stmt, $columnum = 0){
        return $stmt->fetchColumn($columnum);
    }

    /**
     * exec方式 执行sql语句 返回受影响的行数
     * @param  [ string ] $sql [ 一般为delete 或者update 语句 ]
     * @return [ int ]      [ 受影响的行数 ]
     */
    public function exec($sql){
        return $this->_CONN->exec($sql);
    }

    /**
     * query方式 执行sql语句
     * @param  [ string ] $sql [ 一般为 select 语句 ]
     * @return [type]      [ 返回 PDOStatement 对象 ]
     */
    public function query($sql){
        return $this->_CONN->query($sql);
    }

    /**
     * query方式 直接执行sql语句进行查询
     * @param  [ string ] $sql       [ 要执行的sql语句 ]
     * @param  [ string ] $FetchMode [ 数据返回格式 PDO::FETCH_ASSOC | PDO::FETCH_NUM | PDO::FETCH_BOTH | PDO::FETCH_OBJ 等 ]
     * @return [type]                [ 查询返回多条数据结果 （二维数组/对象） ]
     */
    public function querySelect($sql, $FetchMode = PDO::FETCH_ASSOC){
        $exec = $this->_CONN->query($sql);
        return $exec->fetchAll($FetchMode);
    }

    /**
     * query方式 直接执行sql语句进行查询
     * @param  [ string ] $sql       [ 要执行的sql语句 ]
     * @param  [ string ] $FetchMode [ 数据返回格式 PDO::FETCH_ASSOC | PDO::FETCH_NUM | PDO::FETCH_BOTH | PDO::FETCH_OBJ 等 ]
     * @return [type]                [ 查询返回单条数据结果 （一维数组/对象） ]
     */
    public function querySelectOne($sql, $FetchMode = PDO::FETCH_ASSOC){
        $exec = $this->_CONN->query($sql);
        return $exec->fetch($FetchMode);
    }

    /**
     * [ 获取所有表以及字段信息 ]
     * @param  string $type [ 默认：simple 格式返回，至返回表名和字段名称，all的时候返回表名以及字段的所有信息 ]
     * @return [ array ]    [ array 表名和字段信息 ]
     */
    public function getAllTable($type='simple'){
        $sql = "show tables";
        $rows = $this->querySelect($sql);
        $tables = array();
        foreach ($rows as $row){
            $table = empty($row['tables_in_'.$this->_DBNAME]) ? $row[0] : $row['tables_in_'.$this->_DBNAME];
            $items = array();
            $sql = "show columns from {$table}";
            if($type=='simple'){
                $itemsSelect = $this->querySelect($sql);
                foreach ($itemsSelect as $item){
                    $items[]=$item['field'];
                }
            }else{
                $itemsQuery = $this->query($sql);
                $items = $this->fetchAll($itemsQuery, 'field');
            }
            $tables[$table]= $items;
        }
        return $tables;
    }

    /**
     * 启动事务
     * @access public
     * @return void
     */
    public function beginTransaction()
    {
        if (!$this->_CONN) {
            return false;
        }

        //数据rollback 支持
        if (0 == $this->transTimes) {
            $this->_CONN->beginTransaction();
        }
        $this->transTimes++;
        return;
    }

    /**
     * 提交事物 用于非自动提交状态下面的查询提交（在事物启动这状态下commit提交事物）
     * @access public
     * @return boolean
     */
    public function commit()
    {
        if ($this->transTimes > 0) {
            $result           = $this->_CONN->commit();
            $this->transTimes = 0;
            if (!$result) {
                $this->geterror();
                return false;
            }
        }
        return true;
    }

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */
    public function rollback()
    {
        if ($this->transTimes > 0) {
            $result           = $this->_CONN->rollback();
            $this->transTimes = 0;
            if (!$result) {
                $this->error();
                return false;
            }
        }
        return true;
    }

    /**
     * 关闭数据库链接
     */
    public function close() {
        $this->_CONN = null;
    }

    /**
     * 析构函数，自动关闭数据库链接
     */
    public function __destruct(){
        $this->_CONN = null;
    }

    // 获取pdo error
    public function geterror(){
        return $this->getPDOError();
    }

    /**
     * [ 获取 PDO 操作错误 ]
     * @return [type] [ 返回错误 ] 如果没有错误就返回 false 就可以判断返回是否为空 为空的时候就说明没有错误，不为空就返回错误信息
     */
    private function getPDOError(){
        $err_info = flase;
        if ($this->_CONN->errorCode() != '00000'){
            $error = $this->_CONN->errorInfo();
            $err_info = $error[2];
        }
        return $err_info;
    }


}
















?>