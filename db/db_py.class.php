<?php

class db_py {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function get_by_pro_id($pro_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "py where product_id=$pro_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result == false) {
            return mysql_error();
        }
        return $result;
    }

    public function count_py($pro_id, $pym, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "py where product_id=$pro_id and pym='$pym'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);


        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row = $this->db->fetch_assoc($result);
        $count1 = 0;
        if ($row != null) {
            $count1 = $row['c'];
        }
        return $count1;
    }

    public function add_to_array($array, $item) {
        if ($item != "") {
            $array[] = $item;
        }
        return $array;
    }

    public function delete_by_pro_id($pro_id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "py where product_id=$pro_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
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
        return "success";
    }

    public function insert_py_array($pro_id, $pym_array, $log_info) {
        $ar = "";
        for ($i = 0; $i < count($pym_array); $i++) {
            if ($i > 0) {
                $ar = $ar . ",";
            }
            $ar = $ar . "'" . $pym_array[$i] . "'";
        }
        $sql = "delete from " . constant("TABLE_PREFIX") . "py where product_id=$pro_id and pym not in ($ar)";


        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        if ($result == false) {
            return mysql_error();
        }
        for ($i = 0; $i < count($pym_array); $i++) {
            $count1 = $this->count_py($pro_id, $pym_array[$i],$log_info);
            if ($count1 == 0) {
                $this->insert_py($pro_id, $pym_array[$i],$log_info);
            }
        }
        return "success";
    }

    public function insert_py($pro_id, $pym, $log_info) {
        $sql = "insert into " . constant("TABLE_PREFIX") . "py(product_id,pym) values($pro_id,'$pym')";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
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
        return "success";
    }

}

?>