<?php

class db_product_category {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }
    
    function get_sql_for_product($filter,$category,$display,$return_t){
        $ret_cols = "";
        if($return_t=="count"){
            $ret_cols = "count(*) c";
        } else if($return_t=="list"){
            $ret_cols = "a.*,c.c_name";
        }
        $mysql = "select ".$ret_cols." from ". constant("TABLE_PREFIX") ."product a left join ". constant("TABLE_PREFIX")."product_category b "
                ." on a.product_id= b.product_id "
                . " left join ". constant("TABLE_PREFIX") ."category c on b.category_id = c.c_id ";
        $mysql .= " where 1=1 ";
        if($filter!=""){
            if (strpos($filter, " ") != -1) {
                $list1 = strsToArray($filter);
                if (count($list1) == 2 && $list1[0] != "" && $list1[1] != "") {
                    $mysql = $mysql . " and ((a.product_name like '%$list1[0]%' and a.product_model like '%$list1[1]%') or (a.product_name like '%$list1[0]%' and a.product_made like '%$list1[1]%') )";
                } else if (count($list1) == 3 && $list1[0] != "" && $list1[1] != "" && $list1[2] != "") {
                    $mysql = $mysql . " and ((a.product_name like '%$list1[0]%' and a.product_model like '%$list1[1]%' and a.product_made like '%$list1[2]%') or (a.product_name like '%$list1[0]%' and a.product_model like '%$list1[1] "."$list1[2]%')) ";
                } else if (count($list1) == 4 && $list1[0] != "" && $list1[1] != "" && $list1[2] != "" && $list1[3] != "") {
                    $mysql = $mysql . " and (a.product_name like '%$list1[0]%' and a.product_model like '%$list1[1] "."$list1[2]%' and a.product_made like '%$list1[3]%') ";
                } else {
                    $mysql = $mysql . " and (a.product_name like '%$filter%' or a.product_model like '%$filter%' or a.product_made like '%$filter%' ) ";
                }
            } else {
                $mysql = $mysql . " and (a.product_name like '%$filter%' or a.product_model like '%$filter%' or a.product_made like '%$filter%'  or a.product_tags like '%$filter%') ";
            }
        }
        if($category!=""){
            $mysql .= " and b.category_id = ".$category." ";
        }
        if($display==1){
            $mysql .= " and b.category_id is not null";
        } else if($display==3){
            $mysql .= " and b.category_id is null";
        }        
        return $mysql;
    }
    
    public function count_product($filter, $category,$display, $log_info){
        $mysql = $this->get_sql_for_product($filter,$category,$display,"count");                
        
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], 
                $mysql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row = $this->db->fetch_assoc($result);
        $c = 0;
        if($row!=null){
            $c = $row["c"];
        }
        return $c;
    }
    
    
    public function list_product($filter, $category,$display, $page_id, $page_size, $log_info){
        if ($page_id == "") {
            $page_id = 1;
        }
        $limit = $page_size;
        $offset = ($page_id - 1) * $page_size;
        $mysql = $this->get_sql_for_product($filter, $category, $display, "list");
        $mysql = $mysql . " order by a.product_name,a.product_made,a.product_model,a.product_tags";
        $mysql = $mysql . " limit " . $offset . "," . $limit;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], 
                $mysql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
        
    }
    

    public function insert($m_ying_product_category, $log_info) {
        $bean = $m_ying_product_category;
        $mysql = "insert into " . constant("TABLE_PREFIX") . "product_category(`product_id`,`category_id`) values($bean->m_product_id,$bean->m_category_id)";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function delete($product_id,  $log_info) {

        $mysql = "delete from " . constant("TABLE_PREFIX") . "product_category where  product_id = $product_id ";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }
    
    

}
