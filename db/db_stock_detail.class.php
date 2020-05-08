<?php

ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);


require_once(dirname(__FILE__).'/'.'../data/config.php');
require_once(dirname(__FILE__).'/'.'../bean/stock_detail.class.php');
require_once(dirname(__FILE__).'/db_product.class.php');
require_once(dirname(__FILE__).'/db_stock.class.php');

class db_stock_detail {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }
    
    public function insert_for_stock_full($product_id,$quantity,$unit,$log_info){
        $bean = new stock_detail();
        $bean->m_product_id = $product_id;
        $bean->m_action_type = "盘点";
        if($unit==""){
            $bean->m_action_type = "删除库存";
        }
        $bean->m_relate_table = "stock";
        $bean->m_relate_id = $product_id;
        $bean->m_stock_quantity = $quantity;
        $bean->m_stock_unit = $unit; 
        $bean->m_unit = $unit;        
        $cls_product = new db_product($this->db, $this->cls_log);
        $prodcut = $cls_product->get_product($product_id, $log_info);
        $row = $this->db->fetch_assoc($prodcut);
        if ($row) {
            $bean->m_product_name = $row["product_name"];
            $bean->m_product_model = $row["product_model"];
            $bean->m_product_made = $row["product_made"];
        }
        $cls_stock = new db_stock($this->db, $this->cls_log);
        $stock = $cls_stock->get_stock($product_id, $log_info);
        $row = $this->db->fetch_assoc($stock);        
        if ($row) {
            $bean->m_stock_before_quantity = $row["stock_quantity"];   
            $bean->m_stock_before_unit = $row["stock_unit"];   
            if($unit==$row["stock_unit"]){
                $bean->m_quantity = $quantity - $bean->m_stock_before_quantity;   
            } else {
                $bean->m_quantity = 0;
            }
        } else {
            $bean->m_stock_before_quantity = 0;
            $bean->m_stock_before_unit = "";
            $bean->m_quantity = $quantity;
        }
        $bean->m_action_time = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $bean->m_add_time = time();
        return $this->insert($bean, $log_info);        
    }
    
    public function insert_for_stock($product_id,$quantity,$log_info){
        $bean = new stock_detail();
        $bean->m_product_id = $product_id;
        $bean->m_action_type = "盘点";
        $bean->m_relate_table = "stock";
        $bean->m_relate_id = $product_id;
        $cls_product = new db_product($this->db, $this->cls_log);
        $prodcut = $cls_product->get_product($product_id, $log_info);
        $row = $this->db->fetch_assoc($prodcut);
        if ($row) {
            $bean->m_product_name = $row["product_name"];
            $bean->m_product_model = $row["product_model"];
            $bean->m_product_made = $row["product_made"];
        }
        $cls_stock = new db_stock($this->db, $this->cls_log);
        $stock = $cls_stock->get_stock($product_id, $log_info);
        $row = $this->db->fetch_assoc($stock);
        if ($row) {
            $bean->m_stock_before_quantity = $row["stock_quantity"];
            $bean->m_stock_before_unit = $row["stock_unit"];
            $bean->m_stock_unit = $row["stock_unit"];            
            $bean->m_unit = $bean->m_stock_unit;
            $bean->m_quantity = $quantity - $bean->m_stock_before_quantity;            
        }
        $bean->m_stock_quantity = $quantity;
        $bean->m_action_time = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $bean->m_add_time = time();
        return $this->insert($bean, $log_info);
        
        
    }

    public function insert_short($product_id, $action_type, $relate_table, $relate_id, $quantity, $unit, $action_time, $log_info) {
        $bean = new stock_detail();
        $bean->m_product_id = $product_id;
        $bean->m_action_type = $action_type;
        $bean->m_relate_table = $relate_table;
        $bean->m_relate_id = $relate_id;
        $bean->m_quantity = $quantity;
        $bean->m_unit = $unit;
        $cls_product = new db_product($this->db, $this->cls_log);
        $prodcut = $cls_product->get_product($product_id, $log_info);
        $row = $this->db->fetch_assoc($prodcut);
        if ($row) {
            $bean->m_product_name = $row["product_name"];
            $bean->m_product_model = $row["product_model"];
            $bean->m_product_made = $row["product_made"];
        }

        $bean->m_stock_before_quantity = 0;
        $bean->m_stock_unit = $unit;
        $cls_stock = new db_stock($this->db, $this->cls_log);
        $stock = $cls_stock->get_stock($product_id, $log_info);
        $row = $this->db->fetch_assoc($stock);
        if ($row) {
            $bean->m_stock_before_quantity = $row["stock_quantity"];
            $bean->m_stock_before_unit = $row["stock_unit"];
            $bean->m_stock_unit = $row["stock_unit"];
            if ($bean->m_unit == "") {
                $bean->m_unit = $bean->m_stock_unit;
            }
        }
        //$msg .="product_id:$product_id,quantity:$quantity,unit:$unit"."\n";
        $quantity2 = $cls_stock->getQuantity($product_id, $quantity, $unit, $log_info);

        //$msg .="befroe:".$bean->m_stock_before_quantity."\n";
        //$msg .= "quantity:".$quantity2."\n";                

        $stock_quantity = $bean->m_stock_before_quantity + $quantity2;
        //$msg .= "after:".$stock_quantity+"\n";
        //file_put_contents("20180425_log.txt",$msg,FILE_APPEND);  
        $bean->m_stock_quantity = $stock_quantity;
        $bean->m_action_time = $action_time;
        $bean->m_add_time = time();
        return $this->insert($bean, $log_info);
    }

    public function insert_for_instock($product_id, $action_type, $relate_id, $quantity, $unit, $action_time, $log_info) {
        return $this->insert_short($product_id, $action_type, "instock_detail", $relate_id, $quantity, $unit, $action_time, $log_info);
    }

    public function insert_for_sales($product_id, $action_type, $relate_id, $quantity, $unit, $action_time, $log_info) {
        return $this->insert_short($product_id, $action_type, "sales_detail", $relate_id, $quantity, $unit, $action_time, $log_info);
    }

    public function insert($m_ying_stock_detail, $log_info) {
        $bean = $m_ying_stock_detail;

        $mysql = "insert into " . constant("TABLE_PREFIX") . "stock_detail(`product_id`,`action_type`,`relate_table`,`relate_id`,`product_name`,`product_model`,`product_made`,`quantity`,`unit`,`stock_before_quantity`,`stock_before_unit`,`stock_quantity`,`stock_unit`,`action_time`,`add_time`) values($bean->m_product_id,'$bean->m_action_type','$bean->m_relate_table',$bean->m_relate_id,'$bean->m_product_name','$bean->m_product_model','$bean->m_product_made',$bean->m_quantity,'$bean->m_unit',$bean->m_stock_before_quantity,'$bean->m_stock_before_unit',$bean->m_stock_quantity,'$bean->m_stock_unit','" . date("Y-m-d", $bean->m_action_time) . "','" . date("Y-m-d H:i", $bean->m_add_time) . "')";

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
    
    public function count_stock_detail($product_id,$filter1,$start_time,$end_time, $log_info) {
        
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "stock_detail a where a.product_id = ".$product_id . " ";
        if($filter1!=""){
            $sql = $sql . " and (a.product_name like '%$filter1%' or a.product__model like '%$filter1%' or a.product_made like '%filter1%')";
        }
        if ($start_time != "") {
            $sql = $sql . " and a.action_time >= '$start_time' ";
        }
        if($end_time != ""){
            $sql = $sql . " and a.action_time <= '$end_time' ";
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
        if ($result == false) {
            return mysql_error();
        }
        $id = $result['c'];
        return $id;
        
    }

    public function list_stock_detail($page_id, $page_size, $product_id,$filter1,$start_time,$end_time, $log_info) {
        if ($page_id == "") {
            $page_id = 1;
        }
        $limit = $page_size;
        $offset = ($page_id - 1) * $page_size;
        $sql = "select distinct a.* from " . constant("TABLE_PREFIX") . "stock_detail a where a.product_id = ".$product_id . " ";
        if($filter1!=""){
            $sql = $sql . " and (a.product_name like '%$filter1%' or a.product__model like '%$filter1%' or a.product_made like '%filter1%')";
        }
        if ($start_time != "") {
            $sql = $sql . " and a.action_time >= '$start_time' ";
        }
        if($end_time != ""){
            $sql = $sql . " and a.action_time <= '$end_time' ";
        }

        $sql = $sql . " order by a.action_time desc";
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

    public function del_stock_detail($product_id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "stock_detail where product_id = $product_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result) {
            return "success";
        } else {
            return "从" . constant("TABLE_PREFIX") . "stock_detail表删除失败";
        }
    }

}

?>
