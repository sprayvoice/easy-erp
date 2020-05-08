<?php

class db_customer_product {
	
	private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }
 	
 	public function delete_by_id($id){
 		$sql = "update ".constant("TABLE_PREFIX")."customer_product set del_flag=1,del_date=now() where id = $id";
		$this->db->query($sql);
	 }
	 
	 public function get_all_clients($log_info){
		 $sql = "SELECT DISTINCT client_no,client_company FROM ".constant("TABLE_PREFIX")."customer_product";
		 $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
		 $sql, "select", "error", $log_info["user_id"]);
		if ($log_id == false) {
			return false;
		}	else {
			$this->cls_log->update_log_result($log_id, "success");
		}
		$result = $this->db->query($sql);
		$list = array();
		$row_data = $this->db->fetch_assoc($result);
        while($row_data!=null){
            array_push($list,$row_data);
            $row_data = $this->db->fetch_assoc($result);
        }
		return $list;   
	 }
 	
 	public function get_by_id($id,$log_info){
		 $sql = "select * from ".constant("TABLE_PREFIX")."customer_product where id=".$id;
		 $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
			$sql, "select", "error", $log_info["user_id"]);
		 if ($log_id == false) {
		     return false;
		 }	else {
			$this->cls_log->update_log_result($log_id, "success");
		 }
		  $result = $this->db->query($sql);
		  $row = $this->db->fetch_assoc($result);
     	 return $row;
 	}
 	    
	public function gen_by_client_no($client_no,$log_info){
		$sql = "select DISTINCT b.product_id,b.product_name,b.product_model,b.product_made,sales_price,unit,a.client_no,c.client_company from "
			.constant("TABLE_PREFIX")."sales a INNER JOIN ".constant("TABLE_PREFIX")."sales_detail b ON a.batch_id = b.batch_id "
			."INNER JOIN ".constant("TABLE_PREFIX")."client c ON a.client_no = c.client_no "
			."WHERE a.client_no = $client_no AND b.product_id>0 "
			."ORDER BY b.product_name,b.product_model,b.product_made";	
		$log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
			$sql, "select", "error", $log_info["user_id"]);
		 if ($log_id == false) {
		     return false;
		 }	else {
			$this->cls_log->update_log_result($log_id, "success");
		 }

		$result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
		$list = array();
		$row_data = $this->db->fetch_assoc($result);
        while($row_data!=null){
            array_push($list,$row_data);
            $row_data = $this->db->fetch_assoc($result);
        }
		return $list;            
	}
		
	 public function insert_if_not_exist($client_no,$client_company,
	 	$product_id,$product_name,$product_model,$product_made,$price,$unit,$log_info){
			$sql = "select count(*) c from ".constant("TABLE_PREFIX")
				."customer_product where client_no=$client_no and product_id = $product_id"
				." and price_unit='$unit' and del_flag=0";
			$result = $this->db->query($sql);
			$row = $this->db ->fetch_assoc($result );
			$c= $row['c'];
			if($c==0){
				$m_customer_product = new customer_product();
				$m_customer_product->m_client_no = $client_no;
				$m_customer_product->m_client_company = $client_company;
				$m_customer_product->m_product_id = $product_id;
				$m_customer_product->m_product_name = $product_name;
				$m_customer_product->m_product_model = $product_model;
				$m_customer_product->m_product_made = $product_made;			
				$m_customer_product->m_price = $price;
				$m_customer_product->m_price_unit = $unit;
				$this->insert($m_customer_product,$log_info);
			}
	 }

	 public function get_by_client_no($client_no,$filter1,$show_del,$log_info){

		 $sql = "select * from ".constant("TABLE_PREFIX")."customer_product where client_no=".$client_no ;
		 if($filter1!=""){
			$sql .= " and ( product_name like '%$filter1%' or product_model like '%$filter1%' or product_made like '%$filter1%') ";
		 }
		 if($show_del!="1"){
			$sql .= " and del_flag=0 ";
		 }		 
		 $sql .= " ORDER BY product_name,product_model,product_made";
		 $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
			$sql, "select", "error", $log_info["user_id"]);
		 if ($log_id == false) {
		     return false;
		 }
		 $result = $this->db->query($sql);
		 $list = array();
		 $row_data = $this->db->fetch_assoc($result);
         while($row_data!=null){
             array_push($list,$row_data);
             $row_data = $this->db->fetch_assoc($result);
		 }
		 if($result!=false){                    
			$this->cls_log->update_log_result($log_id, "success");
		}
		 return $list;  
	 }

	 public function update_price($id,$price,$fake_price,$tax_price,$fake_tax_price,$log_info){
		$mysql = "update ".constant("TABLE_PREFIX")."customer_product set price = '$price',fake_price='$fake_price',tax_price = '$tax_price',fake_tax_price = '$fake_tax_price' where id = $id";
		$log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
			$mysql, "update", "error", $log_info["user_id"]);
		if ($log_id == false) {
			return false;
		}  
		$result = $this->db->query($mysql);
		if($result!=false){                    
			$this->cls_log->update_log_result($log_id, "success");
		}

	 }

	 public function update($m_customer_product, $log_info) {
		$bean = $m_customer_product;
		$mysql = "update ".constant("TABLE_PREFIX")."customer_product set client_no = $bean->m_client_no,client_company = '$bean->m_client_company',product_id = $bean->m_product_id,product_name = '$bean->m_product_name',product_model = '$bean->m_product_model',product_made = '$bean->m_product_made',price = '$bean->m_price',fake_price = '$bean->m_fake_price',tax_price = '$bean->m_tax_price',fake_tax_price = '$bean->m_fake_tax_price' where  id = $bean->m_id";
		$log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
			$mysql, "update", "error", $log_info["user_id"]);
		if ($log_id == false) {
			return false;
		}  
		$result = $this->db->query($mysql);
		if($result!=false){                    
			$this->cls_log->update_log_result($log_id, "success");
		}
	}
		
	
	
		

	 public function insert($m_customer_product, $log_info) {
		$bean = $m_customer_product;
		$mysql = "insert into ".constant("TABLE_PREFIX")."customer_product(`client_no`,`client_company`,`product_id`,`product_name`,`product_model`,`product_made`,`price`,price_unit,`fake_price`,`tax_price`,`fake_tax_price`,`add_date`,`del_flag`,`del_date`) values($bean->m_client_no,'$bean->m_client_company',$bean->m_product_id,'$bean->m_product_name','$bean->m_product_model','$bean->m_product_made','$bean->m_price','$bean->m_price_unit','$bean->m_fake_price','$bean->m_tax_price','$bean->m_fake_tax_price',now(),0,null)";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $mysql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }  
        $result = $this->db->query($mysql);
        if($result!=false){
            $this->cls_log->update_log_result($log_id, "success");
        }
        $mysql = "select @@IDENTITY as id";
        $result = $this->db->query($mysql);
        $row = $this->db ->fetch_assoc($result );
        $id= $row['id'];
        return $id;
    }
 
}
?>