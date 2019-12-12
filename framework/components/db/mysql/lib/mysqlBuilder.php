<?php

class mysqlBuilder extends mysqlConnect{


    // 获取 mysql 操作的表
    public function table($tableName){
        return $tableName;
    }

    // 字段映射
    public function insert($params = array()){

        foreach ($params as $name => $data) {
            
        }

    }

    // 获取构建的sql字符串
    public function getSql(){
        
    }



}






