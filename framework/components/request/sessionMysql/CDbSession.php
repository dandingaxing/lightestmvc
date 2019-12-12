<?php

class CDbSession{

    // 数据库链接标识
    private $_CONN;

    /**
     * @var boolean whether the session should be automatically started when the session application component is initialized, defaults to true.
     */
    public $autoStart=true;

    // 过期时间
    public $timeOut = 1440;

    private $sessionId;

    // 表名
    private $tableName = "session";
    // sessionid
    private $table_SessionId = "sessionid";
    // sessionKey
    private $table_SessionKey = "sessionkey";
    // sessionValue
    private $table_SessionValue = "sessionvalue";
    // sessionExpire
    private $table_SessionExpire = "expire";

    /**
     * [ 连接数据库 ]
     * @param  [array] $config = array(
     *                           'dbhost'=>'127.0.0.1',    // 数据库链接地址
     *                           'dbport'=>'3306',         // 数据库链接端口
     *                           'dbuser'=>'root',         // 数据库链接用户名
     *                           'dbpassword'=>'',         // 数据库链接密码
     *                           'dbname'=>'',             // 链接使用数据库名称
     *                           'dbcharset'=>'utf8',      // 数据库编码
     *                           'dboptions'=>array(),       // pdo连接参数
     *                           )
     */
    public function __construct($dbconfig){
        $this->connect($dbconfig);
        $this->init();
    }

    // 链接数据库
    private function connect($dbconfig){
        $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=douban;charset=utf8';
        try{
            $this->_CONN = new PDO($dsn, "root", "shaoxing", array(PDO::ATTR_CASE => PDO::CASE_LOWER,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,PDO::ATTR_STRINGIFY_FETCHES => false,PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        }catch(PDOException $e){
            exit('pdo mysql error: '.$e->getMessage());
        }
    }

    /**
     * [getDb 获取链接标识]
     * @return [type] [ mysql 连接标识 $this->_CONN ]
     */
    public function getDb(){
        return $this->_CONN;
    }
    /**
     * Initializes the application component.
     * This method is required by IApplicationComponent and is invoked by application.
     */
    public function init()
    {
        if($this->autoStart)
            $this->open();
    }

    /**
     * Starts the session if it has not started yet.
     */
    public function open()
    {
        @session_start();
        if(session_id()=='')
        {
            return false;
        }
        $this->gcSession();
    }

    // 设置 session
    public function set($key, $value){
        $sessionId = $this->getSessionID();
        $expireDate = date('Y-m-d H:i:s', time()+$this->timeOut);
        $stmt = $this->_CONN->prepare("INSERT INTO {$this->tableName} ({$this->table_SessionId}, {$this->table_SessionKey}, {$this->table_SessionValue}, {$this->table_SessionExpire}) VALUES (:sessionId, :sessionKey, :sessionValue, :sessionExpire)");
        $stmt->bindParam(':sessionId', $sessionId);
        $stmt->bindParam(':sessionKey', $key);
        $stmt->bindParam(':sessionValue', $value);
        $stmt->bindParam(':sessionExpire', $expireDate);
        return ($stmt->execute()) ? $stmt : false;
    }

    // 获取 session
    public function get($keyName){
        $stmt = $this->_CONN->prepare("SELECT {$this->table_SessionId}, {$this->table_SessionKey}, {$this->table_SessionValue}, {$this->table_SessionExpire} FROM {$this->tableName} WHERE {$this->table_SessionId}=:sessionId AND {$this->table_SessionKey}=:sessionKey AND {$this->table_SessionExpire}>:sessionExpire");
        $sessionId = $this->getSessionID();
        $nowDate = date('Y-m-d H:i:s');
        $stmt->bindParam(':sessionId', $sessionId);
        $stmt->bindParam(':sessionKey', $keyName);
        $stmt->bindParam(':sessionExpire', $nowDate);
        $data = $stmt->execute() ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $data ? $execute[$this->table_SessionValue] : null;
    }

    /**
     * Session GC (garbage collection) handler.
     * Do not call this method directly.
     * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function gcSession()
    {
        $nowTime = date('Y-m-d H:i:s');
        $stmt = $this->_CONN->prepare("DELETE FROM {$this->tableName} WHERE {$this->table_SessionExpire}<:sessionExpire");
        $stmt->bindParam(':sessionExpire', $nowTime);
        return $stmt->execute() ? true : false;
    }

    public function remove($keyName, $return=false){
        if ($return) {
            $value = $this->get($keyName);
            return $value;
        }
        $stmt = $this->_CONN->prepare("DELETE FROM {$this->tableName} WHERE {$this->table_SessionId}=:sessionId AND {$this->table_SessionKey}=:sessionKey ");
        $sessionId = $this->getSessionID();
        $stmt->bindParam(':sessionId', $sessionId);
        $stmt->bindParam(':sessionKey', $keyName);
        return $stmt->execute() ? true : false;
    }

    // 删除当前会话下所有session
    public function clearAll(){
        $stmt = $this->_CONN->prepare("DELETE FROM {$this->tableName} WHERE {$this->table_SessionId}=:sessionId ");
        $stmt->bindParam(':sessionId', $sessionId);
        return $stmt->execute() ? true : false;
    }

    /**
     * Frees all session variables and destroys all data registered to a session.
     */
    public function destroy()
    {
        return $this->_CONN->query("TRUNCATE TABLE {$this->tableName}");
    }

    /**
     * @return string the current session name
     */
    public function getSessionName()
    {
        return session_name();
    }

    /**
     * @param string $value the session name for the current session, must be an alphanumeric string, defaults to PHPSESSID
     */
    public function setSessionName($value)
    {
        session_name($value);
    }

    /**
     * @return integer the number of seconds after which data will be seen as 'garbage' and cleaned up, defaults to 1440 seconds.
     */
    public function getTimeout($time)
    {
        return $this->timeOut;
    }

    /**
     * @param integer $value the number of seconds after which data will be seen as 'garbage' and cleaned up
     */
    public function setTimeout($value)
    {
        $this->timeOut = $time;
        return $this;
    }


    /**
     * Returns the number of items in the session.
     * @return integer the number of session variables
     */
    public function getCount()
    {
        $stmt = $this->_CONN->prepare("SELECT COUNT(*) as `counts` FROM {$this->tableName} WHERE {$this->table_SessionId}=:sessionId");
        $sessionId = $this->getSessionID();
        $stmt->bindParam(':sessionId', $sessionId);
        $data = $stmt->execute() ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $data ? $data['counts'] : false;
    }

    /**
     * @return array the list of session variable names
     */
    public function getKeys()
    {
        $stmt = $this->_CONN->prepare("SELECT {$this->table_SessionKey} FROM {$this->tableName} WHERE {$this->table_SessionId}=:sessionId");
        $sessionId = $this->getSessionID();
        $stmt->bindParam(':sessionId', $sessionId);
        $stmt->execute();
        $dataList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $returnArr = array();
        foreach ($dataList as $k => $v) {
            $returnArr[] = $v[$this->table_SessionKey];
        }
        return $returnArr;
    }

    /**
     * @param mixed $key session variable name
     * @return boolean whether there is the named session variable
     */
    public function contains($key)
    {
        $value = $this->get($key);
        return is_null($value) ? false : true;
    }

    /**
     * @return array the list of all session variables in array
     */
    public function getAll()
    {
        $stmt = $this->_CONN->prepare("SELECT {$this->table_SessionKey} FROM {$this->tableName} WHERE {$this->table_SessionId}=:sessionId");
        $sessionId = $this->getSessionID();
        $stmt->bindParam(':sessionId', $sessionId);
        $stmt->execute();
        $dataList = $stmt->fetchAll(PDO::FETCH_ASSOC);        
        return $dataList;
    }

    /**
     * @return boolean whether the session has started
     */
    public function getIsStarted()
    {
        return session_id()!=='';
    }

    /**
     * @return string the current session ID
     */
    public function getSessionID()
    {
        return session_id();
    }

    /**
     * @param string $value the session ID for the current session
     */
    public function setSessionID($value)
    {
        session_id($value);
    }




}

