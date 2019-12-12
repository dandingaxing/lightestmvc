<?php

/**
 * @author by axing
 */
class mysqliBuilder extends mysqliConnect{

    // 设置表名称
    public $table;
    // 设置要查询的字段
    public $field='*';

    // 关联查询所需的on条件
    public $on;

    // 设置排序字段
    public $order;
    // 设置排序方式 ASC  DESC
    public $by = 'ASC';
    // 查询条数
    public $limit;
    // 起始条数
    public $offset;
    // having
    public $having;
    // 分组 groupby 字段
    public $groupby;

    // 是否使用 distinct 关键字 distinct 为去重唯一值
    public $distinct;
    // 设置查询条件
    public $where = '';
    // 最后执行的 sql 语句
    protected $_sql;
    // 设置 and 与 or 关键字
    protected $__logic = '__logic';

    /**
     * [table 设置 mysql 要操作的表]
     * @param  [type] $tableName [ 表名 ]
     * @return [type]            [ this ]
     */
    public function table($tableName){
        $this->table = $this->escapeString($tableName);
        return $this;
    }

    /**
     * [on 设置 mysql 关联查询 on 条件语句  ]
     * @param  [type] $on [ 设置 mysql 表 left join | right join | inner join 关联查询后的 on ]
     * @return [type]     [ this ]
     */
    public function on($on){
        $this->on = $on;
        return $this;
    }

    /**
     * [order 设置 mysql 要排序的字段 ]
     * @param  [type] $order [ 字段名 ]
     * @return [type]        [ this ]
     */
    public function order($order){
        $this->order = $this->escapeString($order);
        return $this;
    }

    /**
     * [by 设置 mysql 要排序的方式 ]
     * @param  [type] $sort [ desc | asc ]
     * @return [type]       [ this ]
     */
    public function by($sort){
        $this->by = $this->escapeString($sort);
        return $this;
    }

    /**
     * [limit 设置 mysql 要查询的limit 条数]
     * @param  int    $limit [ limit ]
     * @return [type]        [ this ]
     */
    public function limit($limit){
        $this->limit = $limit;
        return $this;
    }

    /**
     * [offset 设置 mysql 要查询的起始数 offset ]
     * @param  int    $offset [ offset ]
     * @return [type]         [ this ]
     */
    public function offset($offset){
        $this->offset = $offset;
        return $this;
    }

    /**
     * [having 设置 having 条件参数 having 用法：对已经查询出来（如果有分组，已经分组）后的数据进行条件过滤 ]
     * @param  string $having [ having 条件 ]
     * @return [type]         [ this ]
     */
    public function having($having){
        $this->having = $having;
        return $this;
    }

    /**
     * [groupby 设置 groupby 分组字段 ]
     * @param  string $groupby [ 要分组的字段 ]
     * @return [type]          [ this ]
     */
    public function groupby($groupby){
        $this->groupby = $groupby;
        return $this;
    }



    /**
     * [getBasicWhere 构建设置基本单条 where 条件语句 ]
     * @param  [ string ] $colume [ 字段名 ]
     * @param  [ string|array ] $value  [ 字段值 ]
     * @param  [ string ] $option [ where 运算符 = > < in like BETWEEN >= <=  <> ]
     * @return [ string ]         [ 单条 where 条件语句 ]
     */
    public function getBasicWhere( $colume, $value, $option='='){
        $option = trim( strtolower($option) );
        $value = is_string($value) ? $this->escapeString($value) : $value;

        if ($option=='=') {
            return $colume . " = '" . $value . "' ";
        }elseif ($option=='>') {
            return $colume . " > '" . $value . "' ";
        }elseif ($option=='<') {
            return $colume . " < '" . $value . "' ";
        }elseif ($option=='in') {
            if(is_array($value)){
                $inValue = " ";
                foreach ($value as $k => $v) {
                    $inValue .= "'" . $this->escapeString($v) . "', ";
                }
                $inValue = substr($inValue,0,-2);
                return $colume . " in ( " . $inValue . " ) ";
            }else{
                return $colume . " in ( " . $value . " ) ";
            }
        }elseif ($option=='like') {
            return $colume . " like '" . $value . "' ";
        }elseif ($option=='between') {
            if (is_array($value)) {
                return $colume . " '" . $this->escapeString($value[0]) . "' AND '" . $this->escapeString($value[1]) . "' ";
            }else{
                return $colume . " between " . $value . " ";
            }
        }elseif ($option=='>=') {
            return $colume . " >= '" . $value . "' ";
        }elseif ($option=='<=') {
            return $colume . " <= '" . $value . "' ";
        }elseif ($option=='<>') {
            return $colume . " <> '" . $value . "' ";
        }else{
            return $colume . " = '" . $value . "' ";
        }
    }


    /**
     * [where  查询 第一次 where 条件构建 ]
     * @param  [ string | array ] $where [ __logic 为保留参数[ and | or 不区分大小写 ]设置本次where组的条件方式 ]
     * $where 构建方式如下：
     * 1. $params['User_Id'] = array(array('12', '13', '15'), 'in');
     *    $params['User_Name'] = array('马晓');
     *    $params['User_sex'] = '男';
     *    $params['open_id'] = array('45646', '>');
     *    $params['__logic'] = 'or';
     *    得到：( ( User_Id in (  '12', '13', '15' )  ) OR ( User_Name = '马晓'  ) OR ( User_sex = '男'  ) OR ( open_id > '45646'  )  )
     *
     * @return [type]        [ this ]
     */
    public function where($where){
        if (is_string($where)) {
            $this->where .= $this->escapeString($where);
        }
        $firstWhere = array();
        if (is_array($where)) {
            $logic = $this->getLogic($where);
            foreach ($where as $k => $v) {
                if ($k==$this->__logic) continue;
                if (is_string($v)) {
                    $firstWhere[] = " ( " . $this->getBasicWhere($k, $v) . " ) ";
                }elseif (is_array($v)) {
                    $firstWhere[] = " ( " . $this->getBasicWhere($k, $v[0], isset($v[1]) ? $v[1] : null ) . " ) ";
                }
            }
            $this->where .= ' (' . implode( $logic , $firstWhere ) . ' ) ';
        }
        return $this;
    }


    /**
     * [addWhere 增加 where 条件语句组，一个好用的 where 增加条件组构建方法，使查询变得非常简单 ]
     * @param [string|array] $where [ 要增加的条件语句组 ]
     * $where 构建方式如下：
     * 1. $params['User_Id'] = array(array('12', '13', '15'), 'in');
     *    $params['User_Name'] = array('马晓');
     *    $params['User_sex'] = '男';
     *    $params['open_id'] = array('45646', '>');
     *    $params['__logic'] = 'and';
     *    
     * @param string $type  [ 与上一个条件语句组的连接条件方式[ and | or ] ]
     *
     * 例如：$where 为上面的 $params，$type='or' 得到：
     * or (  ( User_Id in (  '12', '13', '15' )  ) AND ( User_Name = '马晓'  ) AND ( User_sex = '男'  ) AND ( open_id > '45646'  )  )
     * 
     * @return [type]        [ this ]
     */
    public function addWhere($where, $type='AND'){
        if (is_string($where)) {
            $this->where = ' ( ( ' . $this->where . ' ) ' . $type . ' ( ' . $this->escapeString($where) . ' ) )';
        }
        if (is_array($where)) {
            $logic = $this->getLogic($where);
            $whereTmp = array();
            foreach ($where as $k => $v) {
                if ($k==$this->__logic) continue;
                if (is_string($v)) {
                    $whereTmp[] = " ( " . $this->getBasicWhere($k, $v) . " ) ";
                }elseif (is_array($v)) {
                    $whereTmp[] = " ( " . $this->getBasicWhere($k, $v[0], isset($v[1]) ? $v[1] : null ) . " ) ";
                }
            }
            $this->where = ' ( ( ' . $this->where . ' ) ' . $type . ' ( ' . implode($logic, $whereTmp) . ' ) ) ';
        }
        echo $this->where;
        return $this;
    }

    /**
     * [getLogic 获取条件组的条件连接方式 默认为 and ]
     * @param  [type] $where [ where 条件 自动获取 where 中的 $this->__logic 参数 ]
     * @return string        [ AND|OR ]
     */
    public function getLogic($where){
        $__logic = array_key_exists( $this->__logic, $where) ? strtoupper(trim($where[$this->__logic])) : 'AND';
        return in_array($__logic, array('AND', 'OR')) ? $__logic : 'AND';
    }

    /**
     * [setLogic 设置 and 与 or 关键字设置的 保留字段 ]
     * @param [type] $logic [ 变量名称 默认为 __logic 可设置用户自定义名称（如果与用户字段名有冲突） ]
     */
    public function setLogic($logic){
        $this->__logic = $logic;
        return $this;
    }

    /**
     * [select 多值查询 返回二维数组]
     * @return [type] [ false 为sql条件错误 | array() 查询结果 ]
     */
    public function select(){
        $this->_sql = $this->getSelectSql();
        return empty($this->_sql) ? false : $this->baseSelect($this->_sql);
    }

    /**
     * [selectOne 单值查询 返回一维数组]
     * @return [type] [ false 为sql条件错误 | array() 查询结果 ]
     */
    public function selectOne(){
        $this->_sql = $this->getSelectSql();
        return empty($this->_sql) ? false : $this->baseSelectOne($this->_sql);        
    }

    /**
     * [getSelectSql 构建 完整 SELECT 查询语句 (select 与 selectOne 中使用) ]
     * @return [type] [ 完整sql查询语句 ]
     */
    public function getSelectSql(){
        if(empty($this->table)){
            base::error(" tableName can not be empty ", 5000);
        }
        $sql = "SELECT";
        if (!empty($this->distinct)) {
            $sql .= " DISTINCT " . $this->field . " ";
        }else{
            $sql .= " " . $this->field . " ";
        }
        if (!empty($this->table)) {
            $sql .= " FROM " . $this->table . " ";
        }
        if (!empty($this->on)) {
            $sql .= " ON " . $this->on . " ";
        }
        if (!empty($this->where)) {
            $sql .= " WHERE " . $this->where;
        }
        if (!empty($this->groupby)) {
            $sql .= " GROUP BY " . $this->groupby;
        }
        if (!empty($this->having)) {
            $sql .= " HAVING " . $this->having;
        }
        if (!empty($this->order) && !empty($this->by) ) {
            $sql .= " ORDER BY " . $this->order . ' ' . $this->by;
        }
        if (!empty($this->limit)) {
            $sql .= " LIMIT " . $this->limit;
        }
        if (!empty($this->offset)) {
            $sql .= " OFFSET " . $this->offset;
        }
        return $sql;
    }


    /**
     * [findByPk 根据主键进行查找]
     * @param  [type] $pk [ 主键值 ]
     * @return [type] [ false 为sql条件错误 | array() 查询结果 ]
     */
    public function findByPk($pk){
        $pk = $this->escapeString($pk);
        if(empty($this->table)){
            base::error(" tableName can not be empty ", 5000);
        }else{
            $pri = $this->getPRI();
            if (empty($pri)) {
                base::error(" pri column is empty ", 5000);
            }
            $this->_sql = "SELECT ".$this->field." FROM ".$this->table." WHERE ".$pri." = '".$pk."' ";
            return $this->baseSelectOne($this->_sql);
        }
    }




    /**
     * [field 设置要查询的字段 ]
     * @param  string $field [ 设置要查询的字段 ]
     * @return [type]        [ this ]
     */
    public function field($field='*'){
        if (is_array($field)) {
            $this->field = $this->escapeString($field);
        }else{
            $this->field = $this->escapeString($field);
        }
        return $this;
    }


    /**
     * [insert 单条数据插入 ]
     * @param  array  $params [ array( 'colume'=>$values, '字段名' => '字段值' ) ]
     * @return [type]         [ false 插入失败 | 主键id insert 后的主键id ]
     */
    public function insert($params = array()){
        if (empty($params) || empty($this->table) ) {
            base::error(" params and tableName can not be empty ", 5000);
        }
        $inkeys = " ( ";
        $invalues = " ( ";
        foreach ($params as $colume => $param) {
            $inkeys .= $colume.', ';
            $invalues .= " '".$this->escapeString($param)."', ";
        }
        $inkeys = substr($inkeys,0,-2) . ' )';
        $invalues = substr($invalues,0,-2) . ' )';
        $this->_sql = "INSERT INTO {$this->table} {$inkeys} VALUES {$invalues} ;";
        return $this->baseInsert($this->_sql);
    }


    /**
     * [insertall 多条语句批量插入]
     * @param  array  $fields [ 要插入的字段名 array( '字段名1', '字段名2' ) ]
     * @param  array  $params [ 要插入的字段值 array( array('字段名1' => '字段名1值', '字段名2' => '字段名2值'), array('字段名2' => '字段名2值') ) 不限顺序，如果字段值不存在则插入默认 DEFAULT ]
     * @return [type]         [ false 插入失败 | 主键id insert 最后的主键id ]
     */
    public function insertall($fields = array(), $params = array()){
        if (empty($params) || empty($fields) || empty($this->table) ) {
            base::error(" fields and params and tableName can not be empty ", 5000);
        }
        $inkeys = " ( ";
        foreach ($fields as $key => $field) {
            $inkeys .= $field.', ';
        }
        $inkeys = substr($inkeys,0,-2) . ' )';
        // 按照 fields 的顺序进行构造与排序
        $allInvalues = "";
        foreach ($params as $colume => $param) {
            $invalues = " ( ";
            foreach ($fields as $key => $field) {
                $invalues .= isset($param[$field]) ? " '".$this->escapeString($param[$field])."', " : " DEFAULT, ";
            }
            $allInvalues .= substr($invalues,0,-2) . ' ), ';
        }
        $allInvalues = substr($allInvalues,0,-2) . ';';
        $this->_sql = "INSERT INTO {$this->table} {$inkeys} VALUES {$allInvalues} ;";
        return $this->baseInsert($this->_sql);
    }

    /**
     * [updateByPk  根据主键进行字段更新 ]
     * @param  [type] $pk     [ 主键值 ]
     * @param  array  $params [ 要更新的字段内容 array('字段名1'=>'字段值1', '字段名2'=>'字段值2') ]
     * @return [type]         [ false 更新失败 | int 更新影响的行数，注意 可能为 0。所以判断是否执行为 === false ]
     */
    public function updateByPk($pk, $params=array()){
        $pk = $this->escapeString($pk);
        $pri = $this->getPRI();
        $updateSet = "";
        foreach ($params as $colume => $param) {
            $updateSet .= " `".$colume."` = '" . $this->escapeString($param) . "', ";
        }
        $updateSet = substr($updateSet,0,-2);
        $this->_sql = "UPDATE {$this->table} SET {$updateSet} WHERE `{$pri}`='{$pk}' ;";
        return $this->baseUpdate($this->_sql);
    }

    /**
     * [update 根据条件更新]
     * @param  array  $params [ 要更新的字段内容 array('字段名1' =>'字段值1') ]
     * @return [type]         [ false 更新失败 | int 更新影响的行数，注意 可能为 0。所以判断是否执行为 === false ]
     */
    public function update($params=array()){
        $updateSet = "";
        foreach ($params as $colume => $param) {
            $updateSet .= " `".$colume."` = '" . $this->escapeString($param) . "', ";
        }
        $this->_sql = empty($this->where) ? "UPDATE {$this->table} SET {$updateSet} ;" : "UPDATE {$this->table} SET {$updateSet} WHERE {$this->where} ;";
        return $this->baseUpdate($this->_sql);
    }

    /**
     * [deleteByPk 根据主键删除]
     * @param  [type] $pk [ 主键值 ]
     * @return [type]     [ false 更新失败 | int 更新影响的行数，注意 可能为 0。所以判断是否执行为 === false ]
     */
    public function deleteByPk($pk){
        $pk = $this->escapeString($pk);
        $pri = $this->getPRI();
        $this->_sql = "DELETE FROM {$this->table} WHERE `{$pri}`='{$pk}' ;";
        return $this->baseDelete($this->_sql);
    }


    /**
     * [delete 按条件删除]
     * @return [type]     [ false 更新失败 | int 更新影响的行数，注意 可能为 0。所以判断是否执行为 === false ]
     */
    public function delete(){
        $this->_sql = "DELETE FROM {$this->table} WHERE {$this->where} ;";
        return $this->baseDelete($this->_sql);
    }


    /**
     * [getFullColumns 获取表建表信息]
     * @param  string $tableName [ 表名称 ]
     * @return array             [ 二维数组 详细表信息 ]
     */
    public function getFullColumns($tableName=""){
        if (!empty($tableName)) {
            $this->_sql = "SHOW FULL COLUMNS FROM ".$tableName;
            return $this->baseSelect($sql);
        }elseif (!empty($this->table)) {
            $this->_sql = "SHOW FULL COLUMNS FROM ".$this->table;
            return $this->baseSelect($this->_sql);
        }else{
            base::error(" tableName is empty ", 5000);
        }
    }


    /**
     * [getPRI 获取表主键字段名称]
     * @return string [ 主键字段名称 ]
     */
    public function getPRI(){
        $fullColumns = $this->getFullColumns();
        foreach ($fullColumns as $key => $data) {
            if ($data['Key']=='PRI') {
                return $data['Field'];
            }
        }
        return false;
    }


    /**
     * [escapeString 对字符串进行安全过滤]
     * @param  string $str [ 要过滤的字符串 ]
     * @return string      [ 过滤后的字符串，可自定义扩展 ]
     */
    public function escapeString($str){
        return mysqli_real_escape_string($this->getDb(), $str);
    }


    /**
     * [getSql 获取最后执行的sql语句 ]
     * @return string [ 返回最后执行的sql语句 ]
     */
    public function getSql(){
        return $this->_sql;
    }






















}






