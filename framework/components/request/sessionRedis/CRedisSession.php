<?php

class CRedisSession{

    // 数据库链接标识
    private $_redis;

    /**
     * @var boolean whether the session should be automatically started when the session application component is initialized, defaults to true.
     */
    public $autoStart=true;

    // 过期时间
    public $timeOut = 1440;

    private $prefix="PHPSESSID:";


    /**
     * @param string $host
     * @param int $post
     * @param string $auth
     */
    public function __construct($config=array('host'=>'127.0.0.1', 'port'=>6379, 'auth'=>'')) {
        $this->connect($config);
        $this->init();
    }


    /**
     * @param string $host
     * @param int $post
     * @param string $auth
     */
    public function connect($config=array('host'=>'127.0.0.1', 'port'=>6379, 'auth'=>'')) {
        $this->_redis = new Redis();
        $config['host'] = (isset($config['host']) && !empty($config['host'])) ? $config['host'] : '127.0.0.1';
        $config['port'] = (isset($config['port']) && !empty($config['port'])) ? $config['port'] : 6379;
        $this->_redis->connect($config['host'], $config['port']);
        ( isset($config['auth']) && !empty($config['auth']) ) && $this->_redis->auth($config['auth']);
        return $this->_redis;
    }

    /**
     * Initializes the application component.
     * This method is required by IApplicationComponent and is invoked by application.
     */
    public function init()
    {
        if($this->autoStart)
            $this->open();
        $this->gcSession();
    }

    /**
     * [conn 获取redis链接]
     * @return [type] [返回获取redis链接]
     */
    public function conn(){
        return $this->_redis;
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

    /**
     * [hset HASH类型 设置 key]
     * @param string $tableName      表名字key
     * @param string $key            字段名字
     * @param sting $value           值
     * @return [type]                [description]
     */
    public function set($key,$value){
        return $this->_redis->hset($this->prefix.$this->getSessionID(),$key, json_encode(array('data'=>$value, 'expire'=>time()+$this->timeOut)));
    }

    /**
     * [hget 获取]
     * @param  [type] $tableName [表名字key]
     * @param  [type] $field     [字段名字]
     * @return [type]            [值]
     */
    public function get($key){
        $data = $this->_redis->hget($this->prefix.$this->getSessionID(),$key);
        $array = json_decode($data, true);
        if(is_null($array)){
            return false;
        }else{
            if ($array['expire']>time()) {
                return $array['data'];
            }else{
                $this->remove($key);
                return false;
            }
        }
    }

    /**
     * [remove 删除key ]
     * @param  [type]  $key    [ 键值 ]
     * @param  boolean $return [ 是否返回 ]
     * @return [type]          [description]
     */
    public function remove($key, $return=false){
        if ($return) {
            $data = $this->_redis->hget($this->prefix.$this->getSessionID(),$key);
            $array = json_decode($data, true);
            if (is_null($array)) {
                return true;
            }else{
                return $data['data'];
            }
        }
        return $this->_redis->hdel($this->prefix.$this->getSessionID(), $key) ? true : false;
    }

    /**
     * Session GC (garbage collection) handler.
     * Do not call this method directly.
     * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function gcSession(){
        $keysList = $this->_redis->keys($this->prefix."*");
        foreach ($keysList as $sessionId) {
            $keysArr = $this->getKeys($this->prefix.$sessionId);
            foreach ($keysArr as $keyName) {
                if ($this->contains($keyName)) {
                    $this->_redis->hdel($this->prefix.$this->$sessionId, $keyName);
                }
            }
        }
    }

    // 删除当前会话下所有session
    public function clearAll(){
        return $this->_redis->delete($this->prefix.$this->getSessionID());
    }

    /**
     * Frees all session variables and destroys all data registered to a session.
     */
    public function destroy()
    {
        $keysList = $this->_redis->keys($this->prefix."*");
        foreach ($keysList as $key) {
            $this->_redis->delete($key);
        }
    }

    public function getKeys(){
        return $this->_redis->hkeys($this->prefix.$this->getSessionID());
    }

    /**
     * @param mixed $key session variable name
     * @return boolean whether there is the named session variable
     */
    public function contains($key, $sessionId = null){
        if (empty($sessionId)) {
            $sessionId = $this->prefix.$this->getSessionID();
        }
        if($this->_redis->hexists($sessionId, $key)){
            $data = $this->_redis->hget($sessionId, $key);
            $array = json_decode($data, true);
            if (is_array($array) && $array['expire']>time()) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @return array the list of all session variables in array
     */
    public function getAll(){
        $dataList = array();
        $keyArr = $this->getKeys($this->prefix.$this->getSessionID());
        foreach ($keyArr as $key) {
            $d = $this->get($key);
            $d && $dataList[$key] = $d;
        }
        return $dataList ? $dataList : false;
    }

    /**
     * @return string the current session ID
     */
    public function getSessionID()
    {
        return session_id();
    }

    // 析构函数 自动关闭 redis
    public function __destruct(){
        return $this->_redis->close();
    }







}


