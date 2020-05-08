<?php

class db_client {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function list_client($filter1, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "client where 1=1 ";
        if ($filter1 != "") {
            $sql = $sql . " and (client_company like '%$filter1%') ";
        }

        $sql = $sql . " ORDER BY CONVERT(client_company USING gbk) limit 100";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function insert_client(
    $client_company, $client_addr, $tax_no, $bank_name, $client_phone, $remark, $log_info) {
        $sql = "insert into " . constant("TABLE_PREFIX") . "client(client_company,client_addr,tax_no,bank_name,client_phone,remark,add_time) "
                . " values('$client_company','$client_addr','$tax_no','$bank_name','$client_phone','$remark',now())";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result == false) {
            echo mysql_error();
            return 0;
        }
        $sql = "select @@IDENTITY as id";        
        $result = $this->db->query($sql);        
        $row = $this->db->fetch_assoc($result);
        $id = $row['id'];
        return $id;
    }

    public function get_count_from_sales($client_no, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "sales where client_no = " . $client_no;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row = $this->db->fetch_assoc($result);
        $c = $row['c'];
        return $c;
    }

    public function delete_client($client_no, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "client where client_no = " . $client_no;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function update_client(
    $client_no, $client_company, $client_addr, $tax_no, $bank_name, $client_phone, $remark, $log_info) {
        $sql = "update " . constant("TABLE_PREFIX") . "client set client_company='$client_company',client_addr='$client_addr',tax_no='$tax_no',"
                . "bank_name='$bank_name',client_phone='$client_phone',remark='$remark' where client_no=$client_no";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function get_client_by_id($client_no, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "client where client_no = '$client_no'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function get_client_company($log_info) {
        $sql = "select client_company from " . constant("TABLE_PREFIX") . "client";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function get_client_company_filter($filter1, $log_info) {
        $sql = "select client_company from " . constant("TABLE_PREFIX") . "client where client_company like '%" . $filter1 . "%'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function check_exist_company_exact($company_name, $log_info) {
        $sql = "select client_no from " . constant("TABLE_PREFIX") . "client where client_company = '$company_name'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        $row = $this->db->fetch_assoc($result);
        if($row==null)
            return 0;
        $client_no = $row['client_no'];
        return $client_no;
    }

    public function check_exist_company($company_name, $log_info) {
        $sql = "select client_company from " . constant("TABLE_PREFIX") . "client where client_company like '%$company_name%'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], 
                $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        return $result;
    }

}

?>