<?php
class LibDb {
    private static $mConn;
    
    private $mTable;
    private $mWhere;
    private $mOrder;
    private $mLimit;
    private $mParams;
    private $mFields;
    
    public function __construct() {
        if (self::$mConn == null) {
            $port = config("db_port") ? config("db_port") : "3306";
            self::$mConn = new mysqli(config("db_host"), config("db_user"), config("db_pass"), config("db_name"), $port);
            self::$mConn->query("SET NAMES utf8");
        }
        if (self::$mConn->connect_errno) {
            die("db connect_errno:" . self::$mConn->connect_errno);
        }
    }
    /**
     * 执行SQL语句，建议用参数绑定
     * 示例【$this->query("select * from db where name=?",array("张三"));】
     * @param type $sql sql语句
     * @param type $paraArr 绑定参数
     * @param type $maxRow 返回条数
     * @return type
     */
    function query($sql, $paraArr = null, $maxRow = 0) {
        $stmt = self::$mConn->prepare($sql);
        if (is_array($paraArr) && count($paraArr) > 0) {
            $types = "";
            $variables = array();
            $variables[] = &$types;
            for ($n = 0; $n < count($paraArr); $n++) {
                $types .= "s";
                $variables[] = &$paraArr[$n];
                
            }
            call_user_func_array(array($stmt, 'bind_param'), $variables);
        }
        
        $stmt->execute();
        
        if ($stmt->field_count > 0) {
            $rt = $this->fetch($stmt, $maxRow);
            $stmt->close();
            return $rt;
        }else{
            $rt = array('af' => $stmt->affected_rows, 'id' => $stmt->insert_id);
            $stmt->close();
            return $rt; 
        }
    }


    /**
     * 转换查询结果为对象数组列表
     * @param type $result 结果集合
     * @param type $maxRow 返回记录条数
     * @return type
     */
    private function fetch($result, $maxRow = 0) {
        $array = array();
        if ($result instanceof mysqli_stmt) {
            $result->store_result();
 
            $variables = array();
            $data = array();
            $meta = $result->result_metadata();

            while ($field = $meta->fetch_field()){
                $variables[] = &$data[$field->name];
            }
            call_user_func_array(array($result, 'bind_result'), $variables);
            $i = 0;
            while ($result->fetch()) {
                $array[$i] = array();
                foreach ($data as $k => $v){
                    $array[$i][$k] = $v;
                }
                $i++;
                if ($maxRow > 0 && $i >= $maxRow) {
                    break;
                }
            }
        }elseif ($result instanceof mysqli_result) {
            $i = 0;
            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
                $i++;
                if ($maxRow > 0 && $i >= $maxRow) {
                    break;
                }
            }
        }
        return $array;
    }
    
    public function model($table){
        $this->mTable = $table;
        return $this;
    }
    /**
     * 添加sql操作的where条件。
     * @param string $where where语句（不包括where关键字），如："id=?"
     * @param array $param where中的参数值,如 array("1")
     * @return \JoModel
     */
    public function where($where = "0", $param = null) {
        $this->mWhere = $where;
        $this->mOrder = null;
        $this->mLimit = null;
        $this->mFields = null;
        
        $this->mParams = $param;
        return $this;
    }

    /**
     * 添加sql操作的order条件。
     * @param string $order order语句（不包括order by 关键词），如："id desc"
     * @return \JoModel
     */
    public function order($order) {
        $this->mOrder = $order;
        return $this;
    }

    /**
     * 添加sql操作的limit条件
     * @param string $limit limit条件语句（不包括limit关键词）,如:"5,10";
     * @return \JoModel
     */
    public function limit($limit) {
        $this->mLimit = $limit;
        return $this;
    }

    /**
     * 执行查询
     * 需要指定where条件
     * 返回所有符合条件的记录列表。
     * @param array $fields 要返回的属性
     * @return array
     */
    public function select($fields = null) {
        $this->mFields = $fields;
        $sql = "";
        $this->createSql($sql);
        return $this->query($sql, $this->mParams);
    }


    /**
     * 把数据对象添加到数据库。
     * @param array $data 添加的属性值
     * @return boolean 添加成功返回true或新纪录id属性值（自增），失败返回 false. 
     */
    function insert(&$rData) {
        $fields = "";
        $values = "";
        $valueData = array();
        $fg = "";
        foreach ($rData as $key => $value) {
            $fields .= $fg . "`$key`";
            $values .= $fg . "?";

            $valueData[] = $value;
            $fg = ',';
        }
        $sql = " insert into {$this->mTable} ({$fields}) values ({$values}) ";
        $rt = $this->query($sql, $valueData);
        if ($rt["af"]) {
            return $rt["id"] ? $rt["id"] : ($rData["id"] ? $rData["id"] : true);
        }
        return false;
    }

    /**
     * 更新记录(需要指定where条件)，操作成功返回影响记录数（若影响记录数是0，则返回true），否则返回false。
     * @param array $rData 修改的属性和值
     * @return boolean 
     */
    function update(&$rData) {
        $setFields = "";
        $valueData = array();
        $fg = "";
        foreach ($rData as $key => $value) {
            $setFields .= $fg . "`$key`=?";
            $valueData[] = $value;
            $fg = ',';
        }

        if (is_array($this->mParams)) {
            $valueData = array_merge($valueData, $this->mParams);
        }

        $sql = " update {$this->mTable} set {$setFields} where {$this->mWhere} ";
        $rt = $this->query($sql, $valueData);
        if (isset($rt["af"])) {
            return ($rt["af"] > 0) ? $rt["af"] : true;
        }
        return false;
    }

    /**
     * 删除记录(需要指定where条件)，操作成功返回影响记录数（若影响记录数是0，则返回true），否则返回false。
     * @return boolean
     */
    function delete() {
        $sql = " delete from $this->mTable where $this->mWhere ";
        $rt = $this->query($sql, $this->mParams);
        if (isset($rt["af"])) {
            return ($rt["af"] > 0) ? $rt["af"] : true;
        }
        return false;
    }
    
    /**
     * 创建sql语句
     * @param type $sql
     */
    private function createSql(&$sql) {
        $sql = "select ";
        if (is_null($this->mFields)) {
            $sql = $sql . " * ";
        } else {
            $sql = $sql . " $this->mFields ";
        }
        $sql = $sql . " from $this->mTable ";
        $sql = $sql . " where $this->mWhere ";
        if (!is_null($this->mOrder)) {
            $sql = $sql . " order by $this->mOrder ";
        }
        if (!is_null($this->mLimit)) {
            $sql = $sql . " limit $this->mLimit ";
        }
    }
    
    function __destruct() {
        self::$mConn->close();
        self::$mConn = null;
    }
}