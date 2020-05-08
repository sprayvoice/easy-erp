<?php

ini_set("display_errors", "On");

class db_art_cat {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }
    
      public function get_category($c_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "art_cat where cat_id = " . $c_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            echo $this->db->mysql_error();
        }
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function insert($m_ying_category, $log_info) {
        $bean = $m_ying_category;
        $mysql = "insert into " . constant("TABLE_PREFIX") . "art_cat(`cat_show_front`,`cat_sort`,`cat_name`) "
                . "values($bean->m_cat_show_front,$bean->m_cat_sort,'$bean->m_cat_name')";
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
        $mysql = "update " . constant("TABLE_PREFIX") . "art_cat set cat_show_front = $bean->m_cat_show_front,cat_sort = $bean->m_cat_sort,cat_name = '$bean->m_cat_name' where cat_id = $bean->m_cat_id";
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
    	$mysql = "update " . constant("TABLE_PREFIX") . "art_cat set cat_sort = $cat_sort where cat_id = $cat_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function delete($cat_id, $log_info) {

        $mysql = "delete from " . constant("TABLE_PREFIX") . "art_cat where  cat_id = $cat_id";
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

    public function count_category($filter, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "art_cat a where 1=1 ";
        if (trim($filter) != "") {
            $sql .= " and (cat_name like '%$filter%' )";
        }
		
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            return $this->db->mysql_error();
        }

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $result = $this->db->fetch_assoc($result);
        $c = $result['c'];
        return $c;
    }
    
    

    public function list_category_all($where,$log_info) {

        $sql = "select * from " . constant("TABLE_PREFIX") . "art_cat $where order by cat_sort";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            return $this->db->mysql_error();
        }

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
      
        return $result;
    }

    public function list_category($filter, $page_id, $page_size, $log_info) {
        $list = array();
        if ($page_id == "") {
            $page_id = 1;
        }
        $limit = $page_size;
        $offset = ($page_id - 1) * $page_size;
        $sql = "select a.* from " . constant("TABLE_PREFIX") . "art_cat a where 1=1 ";
        if (trim($filter) != "") {
            $sql .= " and (a.cat_name like '%$filter%' )";
        }
        $sql = $sql . " order by a.cat_sort";
        $sql = $sql . " limit " . $offset . "," . $limit;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            return $this->db->mysql_error();
        }
		$row_data = $this->db->fetch_assoc($result);
		 while($row_data!=null){
        	array_push($list,$row_data);
      		$row_data = $this->db->fetch_assoc($result);
    	}
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $list;
    }

}
