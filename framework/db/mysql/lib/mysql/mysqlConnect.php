<?php

/**
 * mysql 方式 连接数据库与基础操作类
 */
class mysqlConnect{

    // 私有链接标识
    private $_conn;

    private $host;
    private $user;
    private $password;
    private $port;
    private $charset;
    private $database;

    /**
     * [__construct 构造方法 传入参数 ]
     * @param array $params [ 链接数据库并赋值给 $this->_conn ]
     */
    public function __construct($params = array()){
        $this->host = isset($params['host']) ? $params['host'] : '127.0.0.1';
        $this->user = isset($params['user']) ? $params['user'] : 'root';
        $this->password = isset($params['password']) ? $params['password'] : '';
        $this->port = isset($params['port']) ? $params['port'] : 3306;
        $this->charset = isset($params['charset']) ? $params['charset'] : "utf8";
        $this->database = isset($params['database']) ? $params['database'] : "";
        $this->_conn = mysql_connect($this->host, $this->user, $this->password, true);
        mysql_select_db($this->database, $this->_conn);
        mysql_query("set names ".$this->charset, $this->_conn ) ;
    }

    /**
     * [getDb 获取链接标识]
     * @return [type] [ mysql 连接标识 $this->_conn ]
     */
    public function getDb(){
        return $this->_conn;
    }

    /**
     * [query  执行sql 语句 ]
     * @param  string $sql [ sql 语句 ]
     * @return [type]      [ false 执行失败，或者 query 执行结果返回标识 ]
     */
    public function query($sql){
        if (empty($sql)) {
            return false;
        }
        $this->sql = $sql;
        $result = mysql_query($this->sql, $this->_conn);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * [baseInsert 添加数据]
     * @param  string $sql [ 要执行的sql语句 ]
     * @return [type]      [ false插入失败，返回新增的主键id ]
     */
    public function baseInsert($sql){
        $query = $this->query($sql);
        return empty($query) ? false : $this->insertId();
    }

    /**
     * [baseUpdate 修改数据]
     * @param  string $sql [ 要执行的sql语句 ]
     * @return [type]      [ false修改失败，返回影响的条数 ]
     */
    public function baseUpdate($sql){
        $query = $this->query($sql);
        return empty($query) ? false : $this->affectedRows();
    }

    /**
     * [insertId 获取最后插入的id ]
     * @return [type] [ 执行 insert 后 获取插入的主键id ]
     */
    public function insertId(){
        return mysql_insert_id($this->_conn);
    }

    /**
     * [affectedRows 获取sql执行后影响的条数，主要用在更新和删除数据中 ]
     * @return int [ 获取sql执行修改影响条数 ]
     */
    public function affectedRows(){
        return mysql_affected_rows($this->_conn);
    }


    /**
     * [baseSelectOne 查询单条数据]
     * @param  [type] $sql  [ 要执行的sql语句 ]
     * @param  [type] $type [ 返回的格式 MYSQL_ASSOC(关联数组) MYSQL_NUM(数字数组) MYSQL_BOTH(同时产生关联和数字数组)，默认为 MYSQL_ASSOC(关联数组) ]
     * @return [type]       [ false查询失败，返回 Array 查询结果 ]
     */
    public function baseSelectOne($sql, $type=MYSQL_ASSOC){
        $query=$this->query($sql);
        return empty($query) ? false : $this->fetchArray($query, $type);
    }

    /**
     * [baseSelect 查询多条数据 ]
     * @param  [type] $sql  [ 要执行的sql语句 ]
     * @param  [type] $type [ 返回的格式 MYSQL_ASSOC(关联数组) MYSQL_NUM(数字数组) MYSQL_BOTH(同时产生关联和数字数组)，默认为 MYSQL_ASSOC(关联数组) ]
     * @return [type]       [ false查询失败，返回 Array 查询结果 ]
     */
    public function baseSelect($sql, $type=MYSQL_ASSOC){
        $query=$this->query($sql);
        if (empty($query)) {
            return false;
        }
        $dataList = array();
        while ( $data = $this->fetchArray($query, $type) ) {
            $dataList[] = $data;
        }
        return $dataList;
    }

    /**
     * [fetchArray 返回查询结果 ]
     * @param  [type] $query [ sql语句执行 query 产生的结果 ]
     * @param  [type] $type  [ 返回的格式 MYSQL_ASSOC(关联数组) MYSQL_NUM(数字数组) MYSQL_BOTH(同时产生关联和数字数组)，默认为 MYSQL_ASSOC(关联数组) ]
     * @return [type]        [ 查询结果 Array ]
     */
    public function fetchArray($query, $type=MYSQL_ASSOC){
        return mysql_fetch_array($query, $type);
    }

    /**
     * [baseDelete 删除数据 ]
     * @param  [type] $sql [ 要删除的sql语句 ]
     * @return [type]      [ false删除失败，删除的条数 ]
     */
    public function baseDelete($sql){
        $query=$this->query($sql);
        return empty($query) ? false : $this->affectedRows();
    }

    /**
     * [transaction 执行事务]
     * @param  array  $sqlArr [ 要执行的sql语句数组 ]
     * @return Boolean        [ 返回 true 或 false ]
     */
    public function transaction($sqlArr = array()){
        if (empty($sqlArr)) {
            return false;
        }
        // 是否提交
        $isCommit = true;
        // 开启事物
        mysql_query("BEGIN", $this->_conn);    // 或者mysql_query("START TRANSACTION");
        foreach ($sqlArr as $key => $sql) {
            if (empty($this->query($sql))) {
                $isCommit = false;
            }
        }
        if($isCommit){
            // 事物提交
            mysql_query("COMMIT", $this->_conn);
        }else{
            // 事物回滚
            mysql_query("ROLLBACK", $this->_conn);
        }
        // 结束事物
        mysql_query("END", $this->_conn);
        return $isCommit;
    }


    /**
     * 析构函数，自动关闭数据库链接
     */
    public function __destruct(){
        mysql_close($this->_conn);
    }


}

