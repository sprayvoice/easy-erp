<?php

require_once(dirname(__FILE__).'/../data/config.php');
require_once ( 'db_log.class.php');
require_once(dirname(__FILE__).'/../bean/big_product_stock.class.php');

class db_big_product_stock {

	private $db;
    private $cls_log;

	 public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function get($id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "big_product_stock where id = " . $id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], 
            $sql, "select", "error", $log_info["user_id"]);
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
        $row = $this->db ->fetch_assoc($result);
        $bean = null;
        if($row){
            $bean = new big_product_stock();
            $bean->m_id = $row['id'];
            $bean->m_product_id = $row['product_id'];
            $bean->m_product_state = $row['product_state'];
            $bean->m_stock_position = $row['stock_position'];
            $bean->m_quantity = $row['quantity'];
            $bean->m_unit = $row['unit'];
            $bean->m_instock_batch_id = $row['instock_batch_id'];
            $bean->m_add_date = $row['add_date'];
            $bean->m_update_date = $row['update_date'];
            $bean->m_b_no = $row["b_no"];
        }     
        return $bean;
    }

  

    public function get_full($product_id,$instock_batch_id,$log_info){
        $sql = "select a.*,DATE_FORMAT(a.add_date,'%Y-%m-%d') add_date_str,DATE_FORMAT(a.update_date,'%Y-%m-%d') update_date_str,b.product_name,b.product_model,b.product_made from "
        .constant("TABLE_PREFIX")."big_product_stock a inner join "
        .constant("TABLE_PREFIX")."product b on a.product_id = b.product_id where a.product_id = $product_id and a.instock_batch_id=$instock_batch_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
            $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        
        $result = $this->db->query($sql);
        
        if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function list_pro_cunt($filter,$stock_state){
        $total = 0;
        $where = ' where 1=1 ';
        if($filter!=""){
            $where .= " and (b.product_name like '%".$filter."%' or b.product_model like '%"
                .$filter."%' or b.product_made like '%".$filter."%' or b.product_tags like '%".$filter."%')";
        }
        if($stock_state!='0' && $stock_state!=''){
            $where .= " and a.product_state='".$stock_state."' ";
        }        
        $sql_c = "select count(*) c from " . constant("TABLE_PREFIX") . "big_product_stock a inner join " . constant("TABLE_PREFIX") 
        . "product b on a.product_id = b.product_id"
            .$where;
        $result = $this->db->query($sql_c);
        if($result!=false){
            $result = $this->db ->fetch_assoc($result );
            $total = $result["c"];
        }
        return $total;
    }

    public function get_by_id($id){
        $sql = "select a.id,a.product_state,a.stock_position,a.quantity,a.unit,a.instock_batch_id,"
        ." b.product_id,b.product_name,b.product_model,b.product_made,b.product_tags  "
        ." from " . constant("TABLE_PREFIX") . "big_product_stock a inner join " . constant("TABLE_PREFIX") 
        . "product b  "
        ." on a.product_id = b.product_id  "
        . " where a.id=".$id;
        $result = $this->db->query($sql);
        return $result;
    }

    public function list_big_pro_stock($filter,$stock_state,$page_id,$page_size){
        if($page_id<1){
            $page_id=1;
        }
        $start = ($page_id-1)*$page_size;       
        $where = ' where 1=1 ';
        if($filter!=""){
            $where .= " and (b.product_name like '%".$filter."%' or b.product_model like '%"
                .$filter."%' or b.product_made like '%".$filter."%' or b.product_tags like '%".$filter."%')";
        }  
        if($stock_state!='0' && $stock_state!=''){
            $where .= " and a.product_state='".$stock_state."' ";
        } 
        $sql = "select a.id,a.product_state,a.stock_position,a.quantity,a.unit,a.instock_batch_id,"
            ." b.product_id,b.product_name,b.product_model,b.product_made,b.product_tags,a.b_no  "
            ." from " . constant("TABLE_PREFIX") . "big_product_stock a inner join " 
            . constant("TABLE_PREFIX") . "product b  "
            ." on a.product_id = b.product_id  "
            . $where
            ." order by b.product_name,b.product_model,b.product_made "
            ." limit ".$start.",".$page_size;
        $result = $this->db->query($sql);
        return $result;
    }

    public function delete( $id, $log_info) {
    
        $mysql = "delete from ".constant("TABLE_PREFIX")."big_product_stock where  id = $id";    
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $mysql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }  
        $result = $this->db->query($mysql);
        if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }
	

	}
	
	public function insert($m_big_product_stock, $log_info) {
        $bean = $m_big_product_stock;
        $bean->m_b_no = $this->get_b_no();
        $mysql = "insert into ".constant("TABLE_PREFIX")."big_product_stock(`product_id`,`product_state`,`stock_position`,`quantity`,`unit`,`instock_batch_id`,`add_date`,`update_date`,`b_no`) values($bean->m_product_id,'$bean->m_product_state','$bean->m_stock_position',$bean->m_quantity,'$bean->m_unit',$bean->m_instock_batch_id,'$bean->m_add_date','$bean->m_update_date','$bean->m_b_no')";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $mysql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }  
        $result = $this->db->query($mysql);
        if($result!=false){
            $this->cls_log->update_log_result($log_id, "success");
        }
    }
    
    public function update($m_big_product_stock, $log_info) {
    	$bean = $m_big_product_stock;
		$mysql = "update ".constant("TABLE_PREFIX")."big_product_stock set product_id = $bean->m_product_id,product_state = '$bean->m_product_state',stock_position = '$bean->m_stock_position',quantity = $bean->m_quantity,unit = '$bean->m_unit',instock_batch_id = $bean->m_instock_batch_id,add_date = '$bean->m_add_date',update_date = '$bean->m_update_date' where  id = $bean->m_id";
		$log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
        	$mysql, "update", "error", $log_info["user_id"]);
        if($log_id == false) {
            return false;
        }  
        $result = $this->db->query($mysql);
        if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }
	
    }

    public function update_lite($id,$product_state,$product_position,$quantity,$unit, $log_info) {
		$mysql = "update ".constant("TABLE_PREFIX")."big_product_stock set product_state = '$product_state',stock_position = '$product_position',quantity = $quantity,unit = '$unit',update_date = now() where  id = $id";
		$log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
        	$mysql, "update", "error", $log_info["user_id"]);
        if($log_id == false) {
            return false;
        }  
        $result = $this->db->query($mysql);
        if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }
	
    }

    public function get_b_no(){
        $mysql = "select concat(date_format(now(),'%Y%m%d%H%i%s'),floor(rand() * 100)) b_no";
        $result = $this->db->query($mysql);
        $result = $this->db ->fetch_assoc($result );
        return $result["b_no"];
    }
    
    public function count_by_batch_id_and_product_id($batch_id,$product_id,$log_info){
        $sql = "select count(*) c from ".constant("TABLE_PREFIX")."big_product_stock a where instock_batch_id ="
        .$batch_id ." and product_id=".$product_id;
		
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], 
            $log_info["action_name"],$sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
		$result = $this->db->query($sql);
        if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }

		if($result==false){
			return mysql_error();
		}
		$result = $this->db ->fetch_assoc($result );
		if($result==false){
			return mysql_error();
        }
        return $result['c'];		
	}

}



