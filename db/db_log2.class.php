<?php

   
//        require_once ( 'mysqli.class.php');

class db_log2 {
    
  

    private $db;
    
     public function __construct($db) {
        $this->db = $db;
    }           
    
 
    public function get_where($page_name, $action_name, $sql_type, $execute_result, $log_batch_id,$start_day,$end_day){
        $sql =  " where 1=1 ";
         if ($page_name != "") {
            $sql .= " and page_name='$page_name' ";
        }
        if ($action_name != "") {
            $sql .= " and action_name='$action_name' ";
        }
        if ($sql_type != "") {
            $sql .= " and sql_type='$sql_type' ";
        }
        if ($execute_result != "") {
            $sql .= " and execute_result='$execute_result' ";
        }
        if ($log_batch_id > 0) {
            $sql .= " and log_batch_id=$log_batch_id ";
        }
        if($start_day!=""){
            $sql .= " and add_date >='$start_day' ";            
        }
        if($end_day!=""){
            $sql .= " and add_date <= '$end_day' ";
        }
        return $sql;
    }

    public function get_log_count($page_name, $action_name, $sql_type, $execute_result, $log_batch_id,$start_day,$end_day) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "log  ";
        $sql .= $this->get_where($page_name, $action_name, $sql_type, $execute_result, $log_batch_id,$start_day,$end_day);        
        $result = $this->db->query($sql);
        if ($result == false) {
            return $this->db->mysql_error();
        }
        $row = $this->db->fetch_assoc($result);
        if ($row != null) {
            $c = $row["c"];
            return $c;
        } else {
            return 0;
        }
    }

    public function get_log_list($page_id, $page_size, $page_name, $action_name, $sql_type, $execute_result, $log_batch_id,$start_day,$end_day) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "log ";
        $sql .= $this->get_where($page_name, $action_name, $sql_type, $execute_result, $log_batch_id,$start_day,$end_day);             
        $offset = ($page_id - 1) * $page_size;
        $limit = $page_size;
        $sql = $sql . " order by log_batch_id desc,log_id limit " . $offset . "," . $limit;      
        $result = $this->db->query($sql);
        return $result;
    }

   public function del_log($page_name, $action_name, $sql_type, $execute_result, $log_batch_id,$start_day,$end_day) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "log  ";
        $sql .= $this->get_where($page_name, $action_name, $sql_type, $execute_result, $log_batch_id,$start_day,$end_day);        
        $result = $this->db->query($sql);
        if ($result == false) {
            return $this->db->mysql_error();
        }
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "log";
        $result = $this->db->query($sql);
        $row = $this->db->fetch_assoc($result);
        $c = 0;
        if ($row != null) {
            $c = $row["c"];
        }
        $sql = "delete from ". constant("TABLE_PREFIX") . "log_batch";                
        $this->db->query($sql);
        return true;
   }

}

?>