<?php

ini_set("display_errors", "On");

class db_category {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }
    
      public function get_category($c_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "category where c_id = " . $c_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            echo mysql_error();
        }
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function insert($m_category, $log_info) {
        $bean = $m_category;
        $mysql = "insert into " . constant("TABLE_PREFIX") . "category(`c_id_parent`,`c_sort`,`c_name`,`c_pic`) "
                . "values($bean->m_c_id_parent,$bean->m_c_sort,'$bean->m_c_name','$bean->m_c_pic')";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], 
                $log_info["page_name"], $log_info["action_name"], $mysql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $mysql = "select @@IDENTITY as id";
        $result = $this->db->query($mysql);
        $row = $this->db->fetch_assoc($result);
        $id = $row['id'];
        return $id;
    }

    public function update($m_category, $log_info) {
        $bean = $m_category;
        $mysql = "update " . constant("TABLE_PREFIX") . "category set c_id_parent = $bean->m_c_id_parent,c_sort = $bean->m_c_sort,c_name = '$bean->m_c_name',c_pic = '$bean->m_c_pic' where c_id = $bean->m_c_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }
    
    public function update_sort($cat_id,$cat_sort,$log_info){
    	$mysql = "update " . constant("TABLE_PREFIX") . "category set c_sort = $cat_sort where c_id = $cat_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function delete($c_id, $log_info) {

        $mysql = "delete from " . constant("TABLE_PREFIX") . "category where  c_id = $c_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
            return "success";
        }
        return $result;
    }

    public function count_category($filter, $parent_id, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "category a where 1=1 ";
        if (trim($filter) != "") {
            $sql .= " and (c_name like '%$filter%' )";
        }
        if ($parent_id > 0) {
            $sql .= " and c_id_parent = $parent_id";
        }

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            return mysql_error();
        }

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $result = $this->db->fetch_assoc($result);
        $c = $result['c'];
        return $c;
    }
    
    

    public function list_category_all($where,$log_info) {

        $sql = "select * from " . constant("TABLE_PREFIX") . "category $where order by c_id_parent,c_sort";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            return mysql_error();
        }

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
      
        return $result;
    }

    public function list_category($filter, $parent_id, $page_id, $page_size, $log_info) {
        if ($page_id == "") {
            $page_id = 1;
        }
        $limit = $page_size;
        $offset = ($page_id - 1) * $page_size;
        $sql = "select a.*,b.c_name c_name_parent from " . constant("TABLE_PREFIX") . "category a left join ".constant("TABLE_PREFIX")."category b on a.c_id_parent = b.c_id where 1=1 ";
        if (trim($filter) != "") {
            $sql .= " and (a.c_name like '%$filter%' )";
        }
        if ($parent_id > 0) {
            $sql .= " and a.c_id_parent = $parent_id";
        }
        $sql = $sql . " order by a.c_id_parent,a.c_sort";
        $sql = $sql . " limit " . $offset . "," . $limit;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            return mysql_error();
        }

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

}
