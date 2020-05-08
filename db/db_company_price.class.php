<?php

class db_company_price {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }  

    public function insert($m_ying_company_price, $log_info) {
        $bean = $m_ying_company_price;
        $mysql = "insert into " . constant("TABLE_PREFIX") . "company_price(`company_name`,`product_name`,`product_price`) values('$bean->m_company_name','$bean->m_product_name','$bean->m_product_price')";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "insert", "error", $log_info["user_id"]);
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

 
    public function delete_company_price($id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "company_price where id = " . $id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    

   


}

?>