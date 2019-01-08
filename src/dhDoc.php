<?php
/**
 * Created by PhpStorm.
 * User: dhailing
 * Date: 2019/1/8
 * Time: 0:42
 */

namespace DhDoc;


class dhDoc
{
    private $type;
    private $hostname;
    private $database;
    private $username;
    private $password;
    private $hostport;
    private $dns;
    private $DB;
    public $tables = [];

    public function __construct($type, $hostname, $database, $username, $password, $hostport)
    {
        $this->type = $type;
        $this->hostname = $hostname;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->hostport = $hostport;

        $this->dns = "{$this->type}:host={$this->hostname};dbname={$this->database}";

        $this->DB = new \PDO($this->dns, $this->username, $this->password);
    }


    /**
     * 获取所有表
     * @return int
     * Created on 2019/1/8 23:25
     * Created by Dh
     */
    public function getTables()
    {
        $sql = "SHOW TABLES";

        $tables = $this->DB->exec($sql);

        $this->tables = $tables;

        return $this->tables;
    }

    /**
     * 创建markdown格式
     * @return bool|string
     * Created on 2019/1/8 23:38
     * Created by Dh
     */
    public function createMarkdown()
    {
        if(!is_array($this->tables)) {
            return false;
        }

        $fullTable = '';
        foreach ($this->tables as $tk => $tv) {
            $fullTableHeader = '|字段名|类型(长度/小数)|为空|额外|默认|备注|'.PHP_EOL.'|:---|:---|:---|:---|:---|:---|:---|'.PHP_EOL;
            $fullFieldsql = "SHOW FULL fields FROM " . $tv;
            $tableFields = $this->DB->exec($fullFieldsql);
            $tableString = '';
            foreach ($tableFields as $fk=>$fv) {
                $tableString .= '|'.$fv['Field'].'|'.$fv['Type'].'|'.$fv['Null'].'|'.$fv['Extra'].'|'.$fv['Default'].'|'.$fv['Comment'].'|'.PHP_EOL;
                $thisTables = $fullTableHeader.$tableString.'***'.PHP_EOL;
            }
            $fullTable .= $thisTables;
        }

        return $fullTable;
    }
}