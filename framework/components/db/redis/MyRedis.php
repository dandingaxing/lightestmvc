<?php
class MyRedis {

    private $_redis;

    /**
     * @param string $host
     * @param int $post
     * @param string $auth
     */
    public function __construct($config=array('host'=>'127.0.0.1', 'port'=>6379, 'auth'=>'')) {
        $this->_redis = new Redis();
        $config['host'] = (isset($config['host']) && !empty($config['host'])) ? $config['host'] : '127.0.0.1';
        $config['port'] = (isset($config['port']) && !empty($config['port'])) ? $config['port'] : 6379;
        $this->_redis->connect($config['host'], $config['port']);
        ( isset($config['auth']) && !empty($config['auth']) ) && $this->_redis->auth($config['auth']);
        return $this->_redis;
    }

    /**
     * [conn 获取redis链接]
     * @return [type] [返回获取redis链接]
     */
    public function conn(){
        return $this->_redis;
    }

    /**
     * 设置值  构建一个字符串
     * @param string $key KEY名称
     * @param string $value  设置值
     * @param int $timeOut 时间  0表示无过期时间
     */
    public function set($key, $value, $timeOut=0) {
        $retRes = $this->_redis->set($key, $value);
        if ($timeOut > 0)
            $redis->expire('$key', $timeOut);
        return $retRes;
    }

    /**
     * [sadd 构建一个集合(无序集合)]
     * @param string $key    [集合Y名称]
     * @param string|array   [$value  值]
     * @return [type]        [description]
     */
    public function sadd($key,$value){
        return $this->_redis->sadd($key,$value);
    }
    
    /**
     * [zadd 构建一个集合(有序集合)]
     * @param  string $key   [集合名称]
     * @param  string|array  [$value  值]
     * @return [type]        [description]
     */
    public function zadd($key,$value){
        return $this->_redis->zadd($key,$value);
    }
    
    /**
     * 取集合对应元素
     * @param string $setName 集合名字
     */
    public function smembers($setName){
        return $this->_redis->smembers($setName);
    }

    /**
     * 构建一个列表(先进后去，类似栈)
     * @param sting $key KEY名称
     * @param string $value 值
     */
    public function lpush($key,$value){
        return $this->_redis->LPUSH($key,$value);
    }
    
    /**
     * 构建一个列表(先进先去，类似队列)
     * @param sting $key KEY名称
     * @param string $value 值
     */
    public function rpush($key,$value){
        return $this->_redis->rpush($key,$value);
    }
    /**
     * 获取所有列表数据（从头到尾取）
     * @param sting $key KEY名称
     * @param int $head  开始
     * @param int $tail     结束
     */
    public function lranges($key,$head,$tail){
        return $this->_redis->lrange($key,$head,$tail);
    }
    
    /**
     * HASH类型
     * @param string $tableName  表名字key
     * @param string $key            字段名字
     * @param sting $value          值
     */
    public function hset($tableName,$field,$value){
        return $this->_redis->hset($tableName,$field,$value);
    }
    
    /**
     * [hget 获取]
     * @param  [type] $tableName [表名字key]
     * @param  [type] $field     [字段名字]
     * @return [type]            [值]
     */
    public function hget($tableName,$field){
        return $this->_redis->hget($tableName,$field);
    }
    
    
    /**
     * 设置多个值
     * @param array $keyArray KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function sets($keyArray, $timeout) {
        if (is_array($keyArray)) {
            $retRes = $this->_redis->mset($keyArray);
            if ($timeout > 0) {
                foreach ($keyArray as $key => $value) {
                    $this->_redis->expire($key, $timeout);
                }
            }
            return $retRes;
        } else {
            return false;
        }
    }

    /**
     * 通过key获取数据
     * @param string $key KEY名称
     */
    public function get($key) {
        $result = $this->_redis->get($key);
        return $result;
    }

    /**
     * 同时获取多个值
     * @param ayyay $keyArray 获key数值
     */
    public function gets($keyArray) {
        if (is_array($keyArray)) {
            return $this->_redis->mget($keyArray);
        } else {
            return false;
        }
    }

    /**
     * 获取所有key名，不是值
     */
    public function keyAll() {
        return $this->_redis->keys('*');
    }

    /**
     * 删除一条数据key
     * @param string $key 删除KEY的名称
     */
    public function del($key) {
        return $this->_redis->delete($key);
    }

    /**
     * 同时删除多个key数据
     * @param array $keyArray KEY集合
     */
    public function dels($keyArray) {
        if (is_array($keyArray)) {
            return $this->_redis->del($keyArray);
        } else {
            return false;
        }
    }
    
    /**
     * 数据自增
     * @param string $key KEY名称
     */
    public function increment($key) {
        return $this->_redis->incr($key);
    }
    
    /**
     * 数据自减
     * @param string $key KEY名称
     */
    public function decrement($key) {
        return $this->_redis->decr($key);
    }
   
    
    /**
     * 判断key是否存在
     * @param string $key KEY名称
     */
    public function isExists($key){
        return $this->_redis->exists($key);
    }

    /**
     * 重命名- 当且仅当newkey不存在时，将key改为newkey ，当newkey存在时候会报错哦RENAME   
     *  和 rename不一样，它是直接更新（存在的值也会直接更新）
     * @param string $Key KEY名称
     * @param string $newKey 新key名称
     */
    public function updateName($key,$newKey){
        return $this->_redis->RENAMENX($key,$newKey);
    }
    
    /**
     * 获取KEY存储的值类型
     * none(key不存在) int(0)  string(字符串) int(1)   list(列表) int(3)  set(集合) int(2)   zset(有序集) int(4)    hash(哈希表) int(5)
     * @param string $key KEY名称
     */
    public function dataType($key){
        return $this->_redis->type($key);
    }


    /**
     * 清空数据
     */
    public function flushAll() {
        return $this->_redis->flushAll();
    }


     
    /**
     * 返回redis对象
     * redis有非常多的操作方法，我们只封装了一部分
     * 拿着这个对象就可以直接调用redis自身方法
     * eg:$redis->redisOtherMethods()->keys('*a*')   keys方法没封
     */
    public function redisOtherMethods() {
        return $this->_redis;
    }

    public function close(){
        return $this->_redis->close();
    }

    /**
     * 析构函数，自动关闭redis链接
     */
    public function __destruct(){
        return $this->_redis->close();
    }

}