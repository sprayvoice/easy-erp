<?php
class mysql_db {
    private $db_host; //数据库主机
    private $db_user; //数据库用户名
    private $db_pwd; //数据库用户名密码
    private $db_database; //数据库名
    private $conn; //数据库连接标识;
    private $result; //执行query命令的结果资源标识
    private $sql; //sql执行语句
    private $row; //返回的条目数
    private $coding; //数据库编码，GBK,UTF8,gb2312
    private $bulletin = true; //是否开启错误记录
    private $show_error = false; //测试阶段，显示所有错误,具有安全隐患,默认关闭
    private $is_error = false; //发现错误是否立即终止,默认true,建议不启用，因为当有问题时用户什么也看不到是很苦恼的
 
    /*构造函数*/
    public function __construct($db_host, $db_user, $db_pwd, $db_database, $conn, $coding) {
        $this->db_host = $db_host;
        $this->db_user = $db_user;
        $this->db_pwd = $db_pwd;
        $this->db_database = $db_database;
        $this->conn = $conn;
        $this->coding = $coding;
        $this->connect();
    }
 
    /*数据库连接*/
    public function connect() {

        $this->conn = mysqli_connect($this->db_host, $this->db_user, $this->db_pwd);
 
        if (!mysqli_select_db( $this->conn,$this->db_database)) {
            if ($this->show_error) {
                $this->show_error("数据库不可用：", $this->db_database);
            }
        }
        mysqli_query($this->conn,"SET NAMES $this->coding");
    }
 
    /*数据库执行语句，可执行查询添加修改删除等任何sql语句*/
    public function query($sql) {
        if ($sql == "") {
            $this->show_error("SQL语句错误：", "SQL查询语句为空");
        }
        $this->sql = $sql;
 
        $result = mysqli_query( $this->conn,$this->sql);
 
        if (!$result) {
            //调试中使用，sql语句出错时会自动打印出来
            if ($this->show_error) {
                $this->show_error("错误SQL语句：", $this->sql);
            }
        } else {
            $this->result = $result;
        }
        return $this->result;
    }
 
    /*创建添加新的数据库*/
    public function create_database($database_name) {
        $database = $database_name;
        $sqlDatabase = 'create database ' . $database;
        $this->query($sqlDatabase);
    }
 
    /*查询服务器所有数据库*/
    //将系统数据库与用户数据库分开，更直观的显示？
    public function show_databases() {
        $rs = $this->query("show databases");
        echo "现有数据库：" . $amount = $this->db_num_rows($rs);
        echo "<br />";
        $i = 1;
        while ($row = $this->fetch_array($rs)) {
            echo "$i $row[Database]";
            echo "<br />";
            $i++;
        }
    }
 
    //以数组形式返回主机中所有数据库名
    public function databases() {
        $result = $this->query("show databases");
        $rsPtr = mysql_list_dbs($this->conn);
        $i = 0;
        $cnt = mysqli_num_rows($rsPtr);
        while ($i < $cnt) {
            $rs[] = mysql_db_name($rsPtr, $i);
            $i++;
        }
        return $rs;
    }
 
    /*查询数据库下所有的表*/
    public function show_tables($database_name) {
        $this->query("show tables");
        echo "现有数据库：" . $amount = $this->db_num_rows($rs);
        echo "<br />";
        $i = 1;
        while ($row = $this->fetch_array($rs)) {
            $columnName = "Tables_in_" . $database_name;
            echo "$i $row[$columnName]";
            echo "<br />";
            $i++;
        }
    }
 
    /*
    mysql_fetch_row()    array  $row[0],$row[1],$row[2]
    mysql_fetch_array()  array  $row[0] 或 $row[id]
    mysql_fetch_assoc()  array  用$row->content 字段大小写敏感
    mysql_fetch_object() object 用$row[id],$row[content] 字段大小写敏感
    */
 
    /*取得结果数据*/
    public function mysql_result_li() {
        return mysqli_result($str);
    }
 
    /*取得记录集,获取数组-索引和关联,使用$row['content'] */
    public function fetch_array($resultt="") {
        if($resultt<>""){
            return mysqli_fetch_array($resultt);
        }else{
        return mysqli_fetch_array($this->result);
        }
    }
 
    //获取关联数组,使用$row['字段名']
    public function fetch_assoc() {
        return mysqli_fetch_assoc($this->result);
    }
 
    //获取数字索引数组,使用$row[0],$row[1],$row[2]
    public function fetch_row() {
        return mysqli_fetch_row($this->result);
    }
 
    //获取对象数组,使用$row->content
    public function fetch_Object() {
        return mysqli_fetch_object($this->result);
    }
 
    //简化查询select
    public function findall($table) {
        $this->query("SELECT * FROM $table");
    }
    
 
    //简化查询select
    public function select($table, $columnName = "*", $condition = '', $debug = '') {
        $condition = $condition ? ' Where ' . $condition : NULL;
        if ($debug) {
            echo "SELECT $columnName FROM $table $condition";
        } else {
            $this->query("SELECT $columnName FROM $table $condition");
        }
    }
 
    //简化删除del
    public function delete($table, $condition, $url = '') {
        if ($this->query("DELETE FROM $table WHERE $condition")) {
            if (!empty ($url))
                $this->Get_admin_msg($url, '删除成功！');
        }
    }
 
    //简化插入insert
    public function insert($table, $columnName, $value, $url = '') {
        if ($this->query("INSERT INTO $table ($columnName) VALUES ($value)")) {
            if (!empty ($url))
                $this->Get_admin_msg($url, '添加成功！');
        }
    }
 
    //简化修改update
    public function update($table, $mod_content, $condition, $url = '') {
        //echo "UPDATE $table SET $mod_content WHERE $condition"; exit();
        if ($this->query("UPDATE $table SET $mod_content WHERE $condition")) {
            if (!empty ($url))
                $this->Get_admin_msg($url);
        }
    }
 
    /*取得上一步 INSERT 操作产生的 ID*/
    public function insert_id() {
        return mysql_insert_id();
    }
 
    //指向确定的一条数据记录
    public function db_data_seek($id) {
        if ($id > 0) {
            $id = $id -1;
        }
        if (!@ mysqli_data_seek($this->result, $id)) {
            $this->show_error("SQL语句有误：", "指定的数据为空");
        }
        return $this->result;
    }
 
    // 根据select查询结果计算结果集条数
    public function db_num_rows() {
        if ($this->result == null) {
            if ($this->show_error) {
                $this->show_error("SQL语句错误", "暂时为空，没有任何内容！");
            }
        } else {
            return mysqli_num_rows($this->result);
        }
    }
 
    // 根据insert,update,delete执行结果取得影响行数
    public function db_affected_rows() {
        return mysqli_affected_rows();
    }
    
    public function mysql_error(){
        return mysqli_error($this->conn);
    }
 
    //释放结果集
    public function free() {
        @ mysqli_free_result($this->result);
    }
 
    //数据库选择
    public function select_db($db_database) {
        return mysqli_select_db($db_database);
    }
 
    //查询字段数量
    public function num_fields($table_name) {
        //return mysql_num_fields($this->result);
        $this->query("select * from $table_name");
        echo "<br />";
        echo "字段数：" . $total = mysql_num_fields($this->result);
        echo "<pre>";
        for ($i = 0; $i < $total; $i++) {
            print_r(mysql_fetch_field($this->result, $i));
        }
        echo "</pre>";
        echo "<br />";
    }
 
    //取得 MySQL 服务器信息
    public function mysql_server($num = '') {
        switch ($num) {
            case 1 :
                return mysqli_get_server_info(); //MySQL 服务器信息
                break;
 
            case 2 :
                return mysqli_get_host_info(); //取得 MySQL 主机信息
                break;
 
            case 3 :
                return mysqli_get_client_info(); //取得 MySQL 客户端信息
                break;
 
            case 4 :
                return mysqli_get_proto_info(); //取得 MySQL 协议信息
                break;
 
            default :
                return mysqli_get_client_info(); //默认取得mysql版本信息
        }
    }
 
    //析构函数，自动关闭数据库,垃圾回收机制
    public function __destruct() {
        if (!empty ($this->result)) {
            $this->free();
        }
        mysqli_close($this->conn);
    } //function __destruct();
 
    /*获得客户端真实的IP地址*/
    function getip() {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else
            if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            } else
                if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
                    $ip = getenv("REMOTE_ADDR");
                } else
                    if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    } else {
                        $ip = "unknown";
                    }
        return ($ip);
    }
    function inject_check($sql_str) { //防止注入
        $check = eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
        if ($check) {
            echo "输入非法注入内容！";
            exit ();
        } else {
            return $sql_str;
        }
    }
    function checkurl() { //检查来路
        if (preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) !== preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) {
            header("Location: http://www.dareng.com");
            exit();
        }
    }
 
}

?>