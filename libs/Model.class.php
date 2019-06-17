<?php

namespace libs;

class Model
{
    protected $pdo;
    protected $table = '';

    /**
     * @content 构造函数
     */

    public function __construct()
    {
        $host = config('database.host');
        $dbname = config('database.dbname');
        $username = config('database.username');
        $password = config('database.password');
        $this -> pdo = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$username,$password);
        if ($this -> table == '') {
            $this -> getTableName();
        }
    }
    /**
     * @content 获取默认数据表的名字
     */
    public function getTableName()
    {
        $name = get_called_class();
        //userModel -> user
        $this -> table = strtolower(\str_replace('Model','',substr($name,strpos($name,'\\')+1)));
    }
    /**
     * @content 设置数据表的名字
     */
    public function setTableName($tableName='')
    {
        $this -> table = $tableName;
    }
    /**
     * @content 替换表名
     */
    public function replaceTableName($sql)
    {
        return str_replace('__table__',$this -> table,$sql);
    }
    /**
     * @content 查询
     */
    public function query($sql,$data=[])
    {
        $sql = $this -> replaceTableName($sql);
        $stm = $this -> pdo -> prepare($sql);
        // dd($stm);
        $res = $stm -> execute($data);
        //返回所有结果
        $arr = $stm -> fetchAll(\PDO::FETCH_ASSOC);
        if (count($arr) == 1) {
            $arr = $arr[0];
        }
        return $arr;

    }
    /**
     * @content 增删改
     */
    public function exec($sql,$data=[])
    {
        $sql = $this -> replaceTableName($sql);

        $stm = $this -> pdo -> prepare($sql);
        $res = $stm -> execute($data);
        return $res;
    }
}