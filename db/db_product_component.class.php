<?php

class db_product_component {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function get_by_product_id($product_id, $log_info) {      
        $sql = "SELECT distinct a.component_product_quantity,b.* FROM " . constant("TABLE_PREFIX") . "product_component a inner join "
                . constant("TABLE_PREFIX") ."product b on a.component_product_id = b.product_id WHERE a.master_product_id = $product_id ";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result1 = $this->db->query($sql);
        if ($result1 != false) {
            while($row = $this->db->fetch_array($result1)){
                $data[]=$row;
            }            
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $data;                                  
    }
    
    public function del_by_master_product_id($master_product_id,$log_info){
        $sql = "delete from ".constant("TABLE_PREFIX")."product_component where master_product_id=$master_product_id";
         $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result1 = $this->db->query($sql);
        if ($result1 != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result1;   
    }

    public function insert($master_product_id,$component_product_id,$component_product_quantity,$log_info){
        $sql = "insert into ".constant("TABLE_PREFIX")."product_component(master_product_id,component_product_id,component_product_quantity) "
                ." values($master_product_id,$component_product_id,$component_product_quantity)";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result1 = $this->db->query($sql);
        if ($result1 != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result1;   
    }

}

?>